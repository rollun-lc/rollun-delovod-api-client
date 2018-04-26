<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07.06.17
 * Time: 14:03
 */

namespace rollun\delovod\Api\DataStores\ConditionBuilder;

use rollun\datastore\DataStore\ConditionBuilder\ConditionBuilderAbstract;
use rollun\datastore\DataStore\DataStoreException;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\Query\AbstractArrayOperatorNode;
use Xiag\Rql\Parser\Node\Query\AbstractLogicOperatorNode;
use Xiag\Rql\Parser\Node\Query\AbstractScalarOperatorNode;
use Xiag\Rql\Parser\Node\Query\LogicOperator\AndNode;

class DelovodConditionBuilder extends ConditionBuilderAbstract
{
    protected $literals = [
        'LogicOperator' => [
            'and' => ['before' => '', 'between' => ',', 'after' => ''],
        ],
        'ArrayOperator' => [
        ],
        'ScalarOperator' => [
            'eq' => ['before' => '{"alias":"', 'between' => '","operator":"=","value":"',  'after' => '"}'],
            'ne' => ['before' => '{"alias":"', 'between' => '","operator":"!=","value":"', 'after' => '"}'],
            'ge' => ['before' => '{"alias":"', 'between' => '","operator":">=","value":"', 'after' => '"}'],
            'gt' => ['before' => '{"alias":"', 'between' => '","operator":">","value":"',  'after' => '"}'],
            'le' => ['before' => '{"alias":"', 'between' => '","operator":"<=","value":"', 'after' => '"}'],
            'lt' => ['before' => '{"alias":"', 'between' => '","operator":"<","value":"',  'after' => '"}'],
            'like' => ['before' => '{"alias":"', 'between' => '","operator":"%","value":"',  'after' => '"}'],
        ]
    ];

    protected $emptyCondition = '{[]}';

    /**
     * Make string with conditions for any supported Query
     *
     * @param AbstractQueryNode $rootQueryNode
     * @return string
     */
    public function __invoke(AbstractQueryNode $rootQueryNode = null)
    {
        if (isset($rootQueryNode)) {
            //for valid json filter.
            return "[".$this->makeAbstractQueryOperator($rootQueryNode) . "]";
        } else {
            return $this->emptyCondition;
        }
    }

    /**
     * Make string with conditions for ArrayOperatorNode
     *
     * @param AbstractArrayOperatorNode $node
     * @return string
     * @throws DataStoreException
     */
    public function makeArrayOperator(AbstractArrayOperatorNode $node)
    {
        $nodeName = $node->getNodeName();
        if (!isset($this->literals['ArrayOperator'][$nodeName])) {
            throw new DataStoreException(
                'The Array Operator not suppoted: ' . $nodeName
            );
        }
        $arrayValues = $node->getValues();
        $strQuery = $this->literals['ArrayOperator'][$nodeName]['before']
            . $this->prepareFieldName($node->getField())
            . $this->literals['ArrayOperator'][$nodeName]['between'];

        foreach ($arrayValues as $value) {
            $strQuery = $strQuery
                . $this->prepareFieldValue($value)
                . $this->literals['ArrayOperator'][$nodeName]['delimiter'];
        }
        $strQuery = rtrim($strQuery, $this->literals['ArrayOperator'][$nodeName]['delimiter']);
        $strQuery = $strQuery . $this->literals['ArrayOperator'][$nodeName]['after'];
        return $strQuery;
    }
}
