<?php

use Jlem\Context\Filters\CommonFilter;
use Jlem\Context\Filters\DefaultsFilter;
use Jlem\Context\Filters\ConditionFilter;
use Jlem\Context\Filters\Condition;
use Jlem\Context\Context;

class ContexTest extends PHPUnit_Framework_Testcase
{
    public function setUp()
    {
        require_once '../vendor/autoload.php';
    }

    protected function getBasicConfig()
    {
        return array(
            'common' => array(
                'show_tuner_truck_module' => true,
                'date_format' => 'M j, Y',
                'comment_query_criteria' => 'Acme\Comment\Criteria\Member', // Give this to a repository
                'show_comment_ip' => false,
                'unique_to_common' => 'value_only_shows_if_common_used'
            ),
            'defaults' => array(
                'UK' => array(
                    'date_format' => 'j M, Y',
                    'show_comment_ip' => 'uk_dont_show_comment_ip'
                ),
                'Honda' => array(
                    'show_tuner_truck_module' => false
                ),
                'Admin' => array(
                    'comment_query_criteria' => 'Acme\Comment\Criteria\Admin', // Give this to a repository
                    'show_comment_ip' => true,
                    'unique_to_admin' => 'value_only_shows_if_Admin_is_in_context'
                ),
                'Moderator' => array(
                    'comment_query_criteria' => 'Acme\Comment\Criteria\Moderator' // Give this to a repository
                )
            ),
            'conditions' => array(
                'admin_uk' => new Condition(array('country' => 'UK', 'user' => 'Admin'),
                                            array('show_tuner_truck_module' => 'admin_uk')),
                'ford_uk' => new Condition(array('country' => 'UK', 'manufacturer' => 'Ford'), 
                                           array('show_tuner_truck_module' => 'ford_uk')),
            )
        );
    }

    public function testBasicMerging()
    {
        $config = $this->getBasicConfig();

        $context = array(
            'user' => 'Admin',     // maybe get this from Session
            'country' => 'UK',         // maybe from a subdomain or user-agent query as part of the request
            'manufacturer' => 'Ford'   // maybe from a query param, route slug, or what have you
        );

        $Context = new Context($context);
            
        $Context->addFilter('common', new CommonFilter($config));
        $Context->addFilter('defaults', new DefaultsFilter($config));
        $Context->addFilter('conditions', new ConditionFilter($config));

        $actual = $Context->get()->items;

        $expected = array(
            'show_tuner_truck_module' => 'ford_uk',
            'date_format' => 'j M, Y',
            'show_comment_ip' => 'uk_dont_show_comment_ip',
            'comment_query_criteria' => 'Acme\Comment\Criteria\Admin',
            'unique_to_admin' => 'value_only_shows_if_Admin_is_in_context',
            'unique_to_common' => 'value_only_shows_if_common_used'
        );

        $this->assertEquals($expected, $actual);
    }

    public function testMergingWithDisabledContext()
    {
        $config = $this->getBasicConfig();

        $context = array(
            'user' => 'Admin',     // maybe get this from Session
            'country' => 'UK',         // maybe from a subdomain or user-agent query as part of the request
            'manufacturer' => 'Ford'   // maybe from a query param, route slug, or what have you
        );

        $Context = new Context($context);
            
        $Context->addFilter('common', new CommonFilter($config));
        $Context->addFilter('defaults', new DefaultsFilter($config));
        $Context->addFilter('conditions', new ConditionFilter($config));

        $Context->disableContext();

        $actual = $Context->get()->items;

        $expected = $config['common'];

        $this->assertEquals($expected, $actual);
    }

    public function testMergingWithRenabledContext()
    {
        $config = $this->getBasicConfig();

        $context = array(
            'user' => 'Admin',     // maybe get this from Session
            'country' => 'UK',         // maybe from a subdomain or user-agent query as part of the request
            'manufacturer' => 'Ford'   // maybe from a query param, route slug, or what have you
        );

        $Context = new Context($context);
            
        $Context->addFilter('common', new CommonFilter($config));
        $Context->addFilter('defaults', new DefaultsFilter($config));
        $Context->addFilter('conditions', new ConditionFilter($config));

        $Context->disableContext();
        $Context->enableContext();

        $actual = $Context->get()->items;

        $expected = array(
            'show_tuner_truck_module' => 'ford_uk',
            'date_format' => 'j M, Y',
            'show_comment_ip' => 'uk_dont_show_comment_ip',
            'comment_query_criteria' => 'Acme\Comment\Criteria\Admin',
            'unique_to_admin' => 'value_only_shows_if_Admin_is_in_context',
            'unique_to_common' => 'value_only_shows_if_common_used'
        );

        $this->assertEquals($expected, $actual);
    }

    public function testMergingWithDisabledCommonFilter()
    {
        $config = $this->getBasicConfig();

        $context = array(
            'user' => 'Admin',     // maybe get this from Session
            'country' => 'UK',         // maybe from a subdomain or user-agent query as part of the request
            'manufacturer' => 'Ford'   // maybe from a query param, route slug, or what have you
        );

        $Context = new Context($context);
            
        $Context->addFilter('common', new CommonFilter($config));
        $Context->addFilter('defaults', new DefaultsFilter($config));
        $Context->addFilter('conditions', new ConditionFilter($config));

        $Context->disableFilter('common');

        $actual = $Context->get()->items;

        $expected = array(
            'show_tuner_truck_module' => 'ford_uk',
            'date_format' => 'j M, Y',
            'show_comment_ip' => 'uk_dont_show_comment_ip',
            'comment_query_criteria' => 'Acme\Comment\Criteria\Admin',
            'unique_to_admin' => 'value_only_shows_if_Admin_is_in_context'
        );

        $this->assertEquals($expected, $actual);
    }

    public function testContextReorderingWithCommaString()
    {
        $config = $this->getBasicConfig();

        $context = array(
            'user' => 'Admin',
            'country' => 'UK',
            'manufacturer' => 'Ford'
        );

        $Context = new Context($context);
            
        $Context->addFilter('common', new CommonFilter($config));
        $Context->addFilter('defaults', new DefaultsFilter($config));
        $Context->addFilter('conditions', new ConditionFilter($config));
        
        $Context->reorderContext('country,user,manufacturer');

        $actual = $Context->get()->items;

        $expected = array(
            'show_tuner_truck_module' => 'ford_uk',
            'date_format' => 'j M, Y',
            'show_comment_ip' => true,
            'comment_query_criteria' => 'Acme\Comment\Criteria\Admin',
            'unique_to_admin' => 'value_only_shows_if_Admin_is_in_context',
            'unique_to_common' => 'value_only_shows_if_common_used'
        );

        $this->assertEquals($expected, $actual);
    }

    public function testContextReorderingWithArray()
    {
        $config = $this->getBasicConfig();

        $context = array(
            'user' => 'Admin',
            'country' => 'UK',
            'manufacturer' => 'Ford'
        );

        $Context = new Context($context);
            
        $Context->addFilter('common', new CommonFilter($config));
        $Context->addFilter('defaults', new DefaultsFilter($config));
        $Context->addFilter('conditions', new ConditionFilter($config));
        
        $Context->reorderContext(array('country', 'user', 'manufacturer'));

        $actual = $Context->get()->items;

        $expected = array(
            'show_tuner_truck_module' => 'ford_uk',
            'date_format' => 'j M, Y',
            'show_comment_ip' => true,
            'comment_query_criteria' => 'Acme\Comment\Criteria\Admin',
            'unique_to_admin' => 'value_only_shows_if_Admin_is_in_context',
            'unique_to_common' => 'value_only_shows_if_common_used'
        );

        $this->assertEquals($expected, $actual);
    }

    public function testContextReorderingWithReducedContext()
    {
        $config = $this->getBasicConfig();

        $context = array(
            'user' => 'Admin',
            'country' => 'UK',
            'manufacturer' => 'Ford'
        );

        $Context = new Context($context);
            
        $Context->addFilter('common', new CommonFilter($config));
        $Context->addFilter('defaults', new DefaultsFilter($config));
        $Context->addFilter('conditions', new ConditionFilter($config));
        
        $Context->reorderContext(array('country'));

        $actual = $Context->get()->items;

        $expected = array(
            'show_tuner_truck_module' => true,
            'date_format' => 'j M, Y',
            'show_comment_ip' => 'uk_dont_show_comment_ip',
            'comment_query_criteria' => 'Acme\Comment\Criteria\Member',
            'unique_to_common' => 'value_only_shows_if_common_used'
        );

        $this->assertEquals($expected, $actual);
    }
}
