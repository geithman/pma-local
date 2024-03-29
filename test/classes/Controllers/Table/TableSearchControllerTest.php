<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Tests for PMA_TableSearch
 *
 * @package PhpMyAdmin-test
 */
namespace PhpMyAdmin\Tests\Controllers\Table;

use PhpMyAdmin\Controllers\Table\TableSearchController;
use PhpMyAdmin\Core;
use PhpMyAdmin\Di\Container;
use PhpMyAdmin\Relation;
use PhpMyAdmin\Tests\PmaTestCase;
use PhpMyAdmin\Tests\Stubs\Response as ResponseStub;
use PhpMyAdmin\Types;
use ReflectionClass;
use stdClass;

/**
 * Tests for PMA_TableSearch
 *
 * @package PhpMyAdmin-test
 */
class TableSearchControllerTest extends PmaTestCase
{
    /**
     * @var PhpMyAdmin\Tests\Stubs\Response
     */
    private $_response;

    /**
     * Setup function for test cases
     *
     * @access protected
     * @return void
     */
    protected function setUp()
    {
        /**
         * SET these to avoid undefined index error
         */
        $_POST['zoom_submit'] = 'zoom';

        $GLOBALS['server'] = 1;
        $GLOBALS['db'] = 'db';
        $GLOBALS['table'] = 'table';
        $GLOBALS['PMA_PHP_SELF'] = 'index.php';
        $relation = new Relation();
        $GLOBALS['cfgRelation'] = $relation->getRelationsParam();
        $GLOBALS['cfg']['Server']['DisableIS'] = false;

        $dbi = $this->getMockBuilder('PhpMyAdmin\DatabaseInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $dbi->types = new Types($dbi);

        $columns =array(
            array(
                'Field' => 'Field1',
                'Type' => 'Type1',
                'Null' => 'Null1',
                'Collation' => 'Collation1',
            ),
            array(
                'Field' => 'Field2',
                'Type' => 'Type2',
                'Null' => 'Null2',
                'Collation' => 'Collation2',
            )
        );
        $dbi->expects($this->any())->method('getColumns')
            ->will($this->returnValue($columns));

        $show_create_table = "CREATE TABLE `pma_bookmark` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `dbase` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
        `user` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
        `label` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
        `query` text COLLATE utf8_bin NOT NULL,
        PRIMARY KEY (`id`),
        KEY `foreign_field` (`foreign_db`,`foreign_table`)
        ) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin "
        . "COMMENT='Bookmarks'";

        $dbi->expects($this->any())->method('fetchValue')
            ->will($this->returnValue($show_create_table));
        $dbi->expects($this->any())->method('escapeString')
            ->will($this->returnArgument(0));

        $GLOBALS['dbi'] = $dbi;

        $this->_response = new ResponseStub();

        $container = Container::getDefaultContainer();
        $container->set('db', 'PMA');
        $container->set('table', 'PMA_BookMark');
        $container->set('dbi', $GLOBALS['dbi']);
        $container->set('response', $this->_response);
        $container->set('searchType', 'replace');
    }

    /**
     * tearDown function for test cases
     *
     * @access protected
     * @return void
     */
    protected function tearDown()
    {

    }

    /**
     * Test for replace
     *
     * @return void
     */
    public function testReplace()
    {
        $container = Container::getDefaultContainer();

        $tableSearch = new TableSearchController(
            $container->get('response'),
            $container->get('dbi'),
            $container->get('db'),
            $container->get('table'),
            "zoom",
            null
        );
        $columnIndex = 0;
        $find = "Field";
        $replaceWith = "Column";
        $useRegex = false;
        $charSet = "UTF-8";
        $tableSearch->replace(
            $columnIndex, $find, $replaceWith, $useRegex, $charSet
        );

        $sql_query = $GLOBALS['sql_query'];
        $result = "UPDATE `PMA_BookMark` SET `Field1` = "
            . "REPLACE(`Field1`, 'Field', 'Column') "
            . "WHERE `Field1` LIKE '%Field%' COLLATE UTF-8_bin";
        $this->assertEquals(
            $result,
            $sql_query
        );
    }

    /**
     * Test for buildSqlQuery
     *
     * @return void
     */
    public function testBuildSqlQuery()
    {
        $_POST['distinct'] = true;
        $_POST['zoom_submit'] = true;
        $_POST['table'] = "PMA";
        $_POST['orderByColumn'] = "name";
        $_POST['order'] = "asc";
        $_POST['customWhereClause'] = "name='pma'";

        $container = Container::getDefaultContainer();

        $class = new ReflectionClass('PhpMyAdmin\Controllers\Table\TableSearchController');
        $method = $class->getMethod('_buildSqlQuery');
        $method->setAccessible(true);
        $tableSearch = new TableSearchController(
            $container->get('response'),
            $container->get('dbi'),
            $container->get('db'),
            $container->get('table'),
            "zoom",
            null
        );

        $sql = $method->invoke($tableSearch);
        $result = "SELECT DISTINCT *  FROM `PMA` WHERE name='pma' "
            . "ORDER BY `name` asc";

        $this->assertEquals(
            $result,
            $sql
        );

        unset($_POST['customWhereClause']);
        $sql = $method->invoke($tableSearch);
        $result = "SELECT DISTINCT *  FROM `PMA` ORDER BY `name` asc";
        $this->assertEquals(
            $result,
            $sql
        );

        $_POST['criteriaValues'] = array(
            'value1',
            'value2',
            'value3',
            'value4',
            'value5',
            'value6',
            'value7,value8'
        );
        $_POST['criteriaColumnNames'] = array(
            'name',
            'id',
            'index',
            'index2',
            'index3',
            'index4',
            'index5',
        );
        $_POST['criteriaColumnTypes'] = array(
            'varchar',
            'int',
            'enum',
            'type1',
            'type2',
            'type3',
            'type4'
        );
        $_POST['criteriaColumnCollations'] = array(
            "char1",
            "char2",
            "char3",
            "char4",
            "char5",
            "char6",
            "char7",
        );
        $_POST['criteriaColumnOperators'] = array(
            "!=",
            ">",
            "IS NULL",
            "LIKE %...%",
            "REGEXP ^...$",
            "IN (...)",
            "BETWEEN"
        );

        $sql = $method->invoke($tableSearch);
        $result = "SELECT DISTINCT *  FROM `PMA` WHERE `name` != 'value1'"
            . " AND `id` > value2 AND `index` IS NULL AND `index2` LIKE '%value4%'"
            . " AND `index3` REGEXP ^value5$ AND `index4` IN (value6) AND `index5`"
            . " BETWEEN value7 AND value8 ORDER BY `name` asc";
        $this->assertEquals(
            $result,
            $sql
        );
    }

    /**
     * Tests for getColumnMinMax()
     *
     * @return void
     * @test
     */
    public function testGetColumnMinMax()
    {
        $GLOBALS['dbi']->expects($this->any())->method('fetchSingleRow')
            ->will($this->returnArgument(0));

        $container = Container::getDefaultContainer();
        $container->set('dbi', $GLOBALS['dbi']);
        $container->factory('PhpMyAdmin\Controllers\Table\TableSearchController');
        $container->alias(
            'TableSearchController', 'PhpMyAdmin\Controllers\Table\TableSearchController'
        );
        $ctrl = $container->get('TableSearchController');

        $result = $ctrl->getColumnMinMax('column');
        $expected = 'SELECT MIN(`column`) AS `min`, '
            . 'MAX(`column`) AS `max` '
            . 'FROM `PMA`.`PMA_BookMark`';
        $this->assertEquals(
            $expected,
            $result
        );
    }

    /**
     * Tests for _generateWhereClause()
     *
     * @return void
     * @test
     */
    public function testGenerateWhereClause()
    {
        $types = $this->getMockBuilder('PhpMyAdmin\Types')
            ->disableOriginalConstructor()
            ->getMock();
        $types->expects($this->any())->method('isUnaryOperator')
            ->will($this->returnValue(false));

        $class = new ReflectionClass('\PhpMyAdmin\Controllers\Table\TableSearchController');
        $method = $class->getMethod('_generateWhereClause');
        $method->setAccessible(true);

        $container = Container::getDefaultContainer();
        $container->factory('\PhpMyAdmin\Controllers\Table\TableSearchController');
        $container->alias(
            'TableSearchController', 'PhpMyAdmin\Controllers\Table\TableSearchController'
        );
        $ctrl = $container->get('TableSearchController');

        $_POST['customWhereClause'] = '`table` = \'PMA_BookMark\'';
        $result = $method->invoke($ctrl);
        $this->assertEquals(
            ' WHERE `table` = \'PMA_BookMark\'',
            $result
        );

        unset($_POST['customWhereClause']);
        $this->assertEquals(
            '',
            $method->invoke($ctrl)
        );

        $_POST['criteriaColumnNames'] = array(
            'b', 'a', 'c', 'd'
        );
        $_POST['criteriaColumnOperators'] = array(
            '<=', '=', 'IS NULL', 'IS NOT NULL'
        );
        $_POST['criteriaValues'] = array(
            '10', '2', '', ''
        );
        $_POST['criteriaColumnTypes'] = array(
            'int(11)', 'int(11)', 'int(11)', 'int(11)'
        );
        $result = $method->invoke($ctrl);
        $this->assertEquals(
            ' WHERE `b` <= 10 AND `a` = 2 AND `c` IS NULL AND `d` IS NOT NULL',
            $result
        );
    }

    /**
     * Tests for getDataRowAction()
     *
     * @return void
     * @test
     */
    public function testGetDataRowAction()
    {
        $_SESSION[' HMAC_secret '] = hash('sha1', 'test');
        $meta_one = new stdClass();
        $meta_one->type = 'int';
        $meta_one->length = 11;
        $meta_two = new stdClass();
        $meta_two->length = 11;
        $meta_two->type = 'int';
        $fields_meta = array(
            $meta_one, $meta_two
        );
        $GLOBALS['dbi']->expects($this->any())->method('getFieldsMeta')
            ->will($this->returnValue($fields_meta));

        $GLOBALS['dbi']->expects($this->any())->method('fetchAssoc')
            ->will(
                $this->returnCallback(
                    function () {
                        static $count = 0;
                        if ($count == 0) {
                            $count++;

                            return array(
                                'col1' => 1,
                                'col2' => 2,
                            );
                        } else {
                            return null;
                        }
                    }
                )
            );

        $container = Container::getDefaultContainer();
        $container->set('dbi', $GLOBALS['dbi']);
        $container->factory('\PhpMyAdmin\Controllers\Table\TableSearchController');
        $container->alias(
            'TableSearchController', 'PhpMyAdmin\Controllers\Table\TableSearchController'
        );
        $ctrl = $container->get('TableSearchController');

        $_POST['db'] = 'PMA';
        $_POST['table'] = 'PMA_BookMark';
        $_POST['where_clause'] = '`col1` = 1';
        $_POST['where_clause_sign'] = Core::signSqlQuery($_POST['where_clause']);
        $expected = array(
            'col1' => 1,
            'col2' => 2
        );
        $ctrl->getDataRowAction();

        $json = $this->_response->getJSONResult();
        $this->assertEquals(
            $expected,
            $json['row_info']
        );
    }
}
