<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07.06.17
 * Time: 13:24
 */

namespace rollun\delovod\Api;

interface DelovodApiInterface
{
    /**
     * Save object.
     * @param array $params
     * @return bool
     */
    public function saveObject(array $params);

    /**
     * Request objects by filter from entity which set in $from
     * @param array $params
     * @return \array[]|mixed
     */
    public function requestObjects(array $params = []);

    /**
     * Get object by id.
     * @param $id
     * @return array
     */
    public function getObject($id);

	/**
	 * @waring RED DOC https://delovod.ua/help/ru/API_001_setDelMark !!!
	 * Get object by id.
	 * @param $id
	 * @return array
	 */
	public function setDelMark($id);
}