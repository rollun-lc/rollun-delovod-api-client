<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07.06.17
 * Time: 12:58
 */

namespace rollun\delovod\Api\DataStores;

use rollun\datastore\DataStore\ConditionBuilder\ConditionBuilderAbstract;
use rollun\delovod\Api\DataStores\ConditionBuilder\DelovodConditionBuilder;
use rollun\delovod\Api\DelovodApiInterface;
use rollun\utils\Json\Serializer;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\Query\LogicOperator\AndNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode;
use Xiag\Rql\Parser\Node\SelectNode;
use Xiag\Rql\Parser\Query;

class AbstractDocuments extends AbstractEntityAbstract
{

}
