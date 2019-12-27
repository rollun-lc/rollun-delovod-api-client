<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 08.06.17
 * Time: 11:07
 */

namespace rollun\delovod\Api\DataStores;

use rollun\datastore\DataStore\ConditionBuilder\ConditionBuilderAbstract;
use rollun\datastore\DataStore\Interfaces\DataStoresInterface;
use rollun\datastore\DataStore\Traits\NoSupportDeleteAllTrait;
use rollun\datastore\DataStore\Traits\NoSupportDeleteTrait;
use rollun\delovod\Api\DataStores\ConditionBuilder\DelovodConditionBuilder;
use rollun\delovod\Api\DelovodApiInterface;
use rollun\utils\Json\Serializer;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\Query\LogicOperator\AndNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode;
use Xiag\Rql\Parser\Node\SelectNode;
use Xiag\Rql\Parser\Query;

abstract class AbstractEntityAbstract implements DataStoresInterface
{
    use NoSupportDeleteAllTrait;
    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $constantHeader;

    /**
     * @var DelovodApiInterface
     */
    protected $delovodApi;

    /**
     * @var DelovodConditionBuilder;
     */
    protected $conditionBuilder;

    /**
     * SaleOrder constructor.
     * @param DelovodApiInterface $delovodApi
     * @param $type
     * @param array $constantHeader
     */
    public function __construct(DelovodApiInterface $delovodApi, $type, array $constantHeader = [])
    {
        $this->type = $type;
        $this->constantHeader = $constantHeader;
        $this->delovodApi = $delovodApi;
        $this->conditionBuilder = new DelovodConditionBuilder();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        $result = $this->query(new Query());
        return new \ArrayIterator($result);
    }

    /**
     * @param Query $query
     * @return \array[]|mixed
     */
    public function query(Query $query)
    {
        $query = clone $query;
        $fields = !is_null($query->getSelect()) ?
            array_merge($query->getSelect()->getFields(), array_keys($this->constantHeader)) : array_keys($this->constantHeader);
        $fields = array_merge($fields, ["id"]);

        $defFilters = [];
        foreach ($this->constantHeader as $name => $value) {
            $defFilters[] = new EqNode($name, $value);
        }
        if (!empty($defFilters)) {
            $setedData = is_null($query->getQuery()) ? [] : [$query->getQuery()];
            $query->setQuery(new AndNode(array_merge($defFilters, $setedData)));
        }

        $filter = Serializer::jsonUnserialize($this->conditionBuilder->__invoke($query->getQuery()));
        $fields = array_merge($fields, array_column($filter, 'alias'));
        $fields = array_unique($fields);

        $data = $this->delovodApi->requestObjects([
            "from" => $this->type,
            "fields" => array_combine($fields, $fields),
            "filters" => $filter
        ]);
        if (!is_null($query->getLimit())) {
            $limit = !is_null($query->getLimit()->getLimit()) ? $query->getLimit()->getLimit() : null;
            $offset = !is_null($query->getLimit()->getOffset()) ? $query->getLimit()->getOffset() : 0;
            $data = array_splice($data, $offset, $limit);
        }
        //TODO: add sort.
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function getIdentifier()
    {
        return 'id';
    }

    /**
     * @inheritdoc
     */
    public function has($id)
    {
        $obj = $this->read($id);
        return isset($obj) && !empty($obj);
    }

    /**
     * @param $id
     * @return array
     */
    public function read($id)
    {
        return $this->delovodApi->getObject($id);
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        $result = $this->query(new Query());
        return count($result);
    }

    /**
     * @param int|string $id
     * @return array|void
     */
    public function delete($id)
    {
        $this->delovodApi->setDelMark($id);
    }

    /**
     * @inheritdoc
     */
    public function update($itemData, $createIfAbsent = false)
    {
        return $this->create($itemData, true);
    }

    /**
     * @param array $doc
     * @param bool $ifExist
     * @return bool
     */
    public function create($doc, $ifExist = false)
    {
        //$this->constantHeader['id'] = $this->type;
        $doc['header'] = isset($doc['header']) ? array_merge(['id' => $this->type], $this->constantHeader, $doc['header']) : [];
        $doc['tableParts'] = $doc['tableParts'] ?? [];
        $doc['saveType'] = $doc['saveType'] ?? 0;
        $result = $this->delovodApi->saveObject($doc);
        return $result;
    }
}
