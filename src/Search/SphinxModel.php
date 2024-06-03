<?php
namespace Snow\StuWeb\repositories;

use basic\exceptions\SphinxException;
use sphinx\SphinxClient;

class SphinxModel
{
    private SphinxClient $client;

    protected int $page = 1;

    protected int $pageSize = 20;

    protected string $order = '';

    protected string $index = '';

    private string $query = '';

    private array $where = [];

    private array $searchWeightFields = [];

    protected function __construct($host = '127.0.0.1', $port = '9312')
    {
        $this->client = new SphinxClient();
        $this->client->SetServer ($host, $port);
        $this->client->SetConnectTimeout ( 5 );
        $this->client->SetArrayResult ( true );
        $this->client->SetMatchMode ( SPH_MATCH_EXTENDED2 );
        $this->client->SetRankingMode ( SPH_RANK_PROXIMITY_BM25 );
    }

    public static function instance()
    {
        return new static();
    }

    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }

    /**
     * 构造条件查询
     * @param $field
     * @param $op
     * @param $condition
     * @return $this
     */
    public function where($field, $op, $condition = null)
    {
        if (is_array($field)) {
            foreach ($field as $key => $value) {
                if (is_string($key)) {
                    $this->insideWhere($key, '=', $value);
                    continue;
                }
                $this->insideWhere($value[0], $value[1], $value[2]);
            }
            return $this;
        }
        if ($condition === null) {
            $condition = $op;
            $op = '=';
        }
        $this->insideWhere($field, $op, $condition);

        return $this;
    }

    /**
     * 关键字搜索
     * @param $fields
     * @param string $keyword
     * @return $this
     */
    public function whereLike($fields, string $keyword)
    {
        $fields = (array) $fields;
        $queryStr = array_reduce($fields, function ($carry, $item) use ($keyword) {
            return "$carry@$item($keyword) | ";
        }, '');
        $queryStr = substr($queryStr, 0, strlen($queryStr) - 2);
        $this->query = $queryStr;

        return $this;
    }

    /**
     * 分页查询
     * @param int $page
     * @param int $pageSize
     * @return $this
     */
    public function page(int $page, int $pageSize)
    {
        $pageSize = min($pageSize, 1000);
        $this->page = $page;
        $this->pageSize = $pageSize;

        return $this;
    }

    /**
     * @param $order
     * @return $this
     */
    public function order($order)
    {
        $orderStr = '';
        if (is_array($order)) {
            foreach ($order as $key => $value) {
                $orderStr .= "$key $value ";
            }
            $this->order = trim($orderStr);
            return $this;
        }

        $this->order = $order;
        return $this;
    }

    /**
     * @param string $field
     * @param string $operate
     * @param $value
     * @return void
     */
    protected function insideWhere(string $field, string $operate, $value)
    {
        $position = strpos($field, '.');
        if ($position !== false) {
            $field = substr($field, $position + 1);
        }

        if (!in_array($operate, ['!=', '<>', '>', '<', '=', 'between'])) {
            throw new SphinxException("$operate operate is not allowed, now we only support: !=, <>, >, <, =");
        }

        if (in_array($operate, ['!=', '<>', '='])) {
            $value = (array) $value;
        }

        $this->where[] = [$field, $operate, $value];
    }

    /**
     * 搜索高亮设置
     * @param $data
     * @param $searchFields
     * @param $keyword
     * @param $opts
     * @return mixed|void[]
     */
    public function highlight($data, $searchFields, $keyword, $opts = [])
    {
        $searchTexts = array_reduce($searchFields, function ($result, $item) use ($data) {
            return array_merge($result, array_column($data, $item));
        }, []);
        $searchTexts = array_values(array_filter($searchTexts));
        $index = $this->index;
        if (($indexSep = strpos($index, ',')) !== false) {
            $index = substr($index, 0, $indexSep);
        }
        $highlights = $this->client->BuildExcerpts($searchTexts, $index, $keyword, $opts);
        if (!$highlights) return $data;
        $highlights = array_combine($searchTexts, $highlights);

        return array_map(function($item) use ($searchFields, $searchTexts, $highlights) {
            foreach ($searchFields as $searchField) {
                if (in_array($item[$searchField], $searchTexts)) {
                    $item[$searchField] = $highlights[$item[$searchField]] ?? '';
                }
            }

            return $item;
        }, $data);
    }

    public function setFieldWeights(array $weightFields)
    {
        $this->searchWeightFields = $weightFields;
        return $this;
    }

    public function select()
    {
        foreach ($this->where as $match) {
            switch ($match[1]) {
                case '!=':
                case '<>':
                    $this->client->SetFilter($match[0], (array) $match[2], true);
                    break;
                case '>':
                    $this->client->SetFilterRange($match[0], $match[2], PHP_INT_MAX);
                    break;
                case '<':
                    $this->client->SetFilterRange($match[0], PHP_INT_MIN, $match[2]);
                    break;
                case 'between':
                    $this->client->SetFilterRange($match[0], $match[2], $match[3]);
                    break;
                default:
                    $this->client->SetFilter($match[0], (array)$match[2]);
            }
        }

        if ($this->order) {
            $this->client->SetSortMode(SPH_SORT_EXTENDED, $this->order);
        }

        $this->client->SetLimits(($this->page - 1) * $this->pageSize, $this->pageSize);

        if ($this->searchWeightFields) {
            $this->client->SetMatchMode(SPH_MATCH_EXTENDED);
            $this->client->SetRankingMode(SPH_RANK_PROXIMITY);
            $this->client->SetFieldWeights($this->searchWeightFields);
            $this->client->SetSortMode(SPH_SORT_EXPR, '@weight');
        }

        $res = $this->client->Query($this->query, $this->index);
        if (empty($res['matches'])) {
            return ['ids' => [], 'total' => 0, 'words' => []];
        }

        return ['ids' => array_column($res['matches'], 'id'), 'total' => $res['total'], 'words' => array_keys($res['words'])];
    }

}
