<?php
namespace Tests\Unit\Modules\Accounts;
use Codeception\Test\Unit;

class AccountTest extends Unit 
{
    public function _before()
    {
        /** @var \Account */
        $this->account = new \Account();
    }

    public function testAccount()
    {
        $this->assertInstanceOf('Account', $this->account);
        $this->assertInstanceOf('Company', $this->account);
        $this->assertInstanceOf('SugarBean', $this->account);
        $this->assertTrue(is_array($this->account->field_name_map));
        $this->assertTrue(is_array($this->account->field_defs));
    }

    public function testget_summary_text()
    {
        $name = $this->account->get_summary_text();
        $this->assertEquals(null, $name);

        //test with  name attribute set
        $this->account->name = 'test account';
        $name = $this->account->get_summary_text();
        $this->assertEquals('test account', $name);
    }

    public function testget_contacts()
    {
        //execute the method and verify that it returns an array
        $contacts = $this->account->get_contacts();
        $this->assertTrue(is_array($contacts));
    }

    public function testfill_in_additional_list_fields()
    {
        //execute the method and test if it works and does not throws an exception.
        try {
            $this->account->fill_in_additional_list_fields();
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail();
        }
    }

    public function testfill_in_additional_detail_fields()
    {
        //execute the method and test if it works and does not throws an exception.
        try {
            $this->account->fill_in_additional_detail_fields();
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail();
        }
    }

    public function testbuild_generic_where_clause()
    {
        //execute the method with a string as parameter and verify that it retunrs expected results
        $expected = "accounts.name like 'value%'";
        $actual = $this->account->build_generic_where_clause('value');
        $this->assertSame($expected, $actual);

        //execute the method with number as parameter and verify that it retunrs expected results
        $expected = "accounts.name like '1234%' or accounts.phone_alternate like '%1234%' or accounts.phone_fax like '%1234%' or accounts.phone_office like '%1234%'";
        $actual = $this->account->build_generic_where_clause('1234');
        $this->assertSame($expected, $actual);
    }

    public function testcreate_export_query()
    {
        $this->markTestSkipped('Move to Functional');
        //execute the method with empty strings and verify that it retunrs expected results
        $expected = "SELECT\n                                accounts.*,\n                                email_addresses.email_address email_address,\n                                '' email_addresses_non_primary, accounts.name as account_name,\n                                users.user_name as assigned_user_name ,accounts_cstm.jjwg_maps_lng_c,accounts_cstm.jjwg_maps_lat_c,accounts_cstm.jjwg_maps_geocode_status_c,accounts_cstm.jjwg_maps_address_c FROM accounts LEFT JOIN users\n	                                ON accounts.assigned_user_id=users.id  LEFT JOIN  email_addr_bean_rel on accounts.id = email_addr_bean_rel.bean_id and email_addr_bean_rel.bean_module='Accounts' and email_addr_bean_rel.deleted=0 and email_addr_bean_rel.primary_address=1  LEFT JOIN email_addresses on email_addresses.id = email_addr_bean_rel.email_address_id  LEFT JOIN accounts_cstm ON accounts.id = accounts_cstm.id_c where ( accounts.deleted IS NULL OR accounts.deleted=0 )";
        $actual = $this->account->create_export_query('', '');
        $this->assertSame($expected, $actual);

        //execute the method with valid parameter values and verify that it retunrs expected results
        $expected = "SELECT\n                                accounts.*,\n                                email_addresses.email_address email_address,\n                                '' email_addresses_non_primary, accounts.name as account_name,\n                                users.user_name as assigned_user_name ,accounts_cstm.jjwg_maps_lng_c,accounts_cstm.jjwg_maps_lat_c,accounts_cstm.jjwg_maps_geocode_status_c,accounts_cstm.jjwg_maps_address_c FROM accounts LEFT JOIN users\n	                                ON accounts.assigned_user_id=users.id  LEFT JOIN  email_addr_bean_rel on accounts.id = email_addr_bean_rel.bean_id and email_addr_bean_rel.bean_module='Accounts' and email_addr_bean_rel.deleted=0 and email_addr_bean_rel.primary_address=1  LEFT JOIN email_addresses on email_addresses.id = email_addr_bean_rel.email_address_id  LEFT JOIN accounts_cstm ON accounts.id = accounts_cstm.id_c where (name not null) AND ( accounts.deleted IS NULL OR accounts.deleted=0 ) ORDER BY accounts.name";
        $actual = $this->account->create_export_query('name', 'name not null');
        $this->assertSame($expected, $actual);
    }

    public function testset_notification_body()
    {

        //execute the method and test if populates provided sugar_smarty
        $result = $this->account->set_notification_body(new \Sugar_Smarty(), new \Account());
        $this->assertInstanceOf('Sugar_Smarty', $result);
        $this->assertNotEquals(new \Sugar_Smarty(), $result);
    }

    public function testbean_implements()
    {

        $this->assertTrue($this->account->bean_implements('ACL')); //test with valid value
        $this->assertFalse($this->account->bean_implements('')); //test with empty value
        $this->assertFalse($this->account->bean_implements('Basic'));//test with invalid value
    }

    public function testget_unlinked_email_query()
    {
        //without setting type parameter
        $expected = "SELECT emails.id FROM emails  JOIN (select DISTINCT email_id from emails_email_addr_rel eear\n\n	join email_addr_bean_rel eabr on eabr.bean_id ='' and eabr.bean_module = 'Accounts' and\n	eabr.email_address_id = eear.email_address_id and eabr.deleted=0\n	where eear.deleted=0 and eear.email_id not in\n	(select eb.email_id from emails_beans eb where eb.bean_module ='Accounts' and eb.bean_id = '')\n	) derivedemails on derivedemails.email_id = emails.id";
        $actual = $this->account->get_unlinked_email_query();
        $this->assertSame($expected, $actual);

        //with type parameter set
        $expected = array('select' => 'SELECT emails.id ',
                           'from' => 'FROM emails ',
                           'where' => '',
                           'join' => " JOIN (select DISTINCT email_id from emails_email_addr_rel eear\n\n	join email_addr_bean_rel eabr on eabr.bean_id ='' and eabr.bean_module = 'Accounts' and\n	eabr.email_address_id = eear.email_address_id and eabr.deleted=0\n	where eear.deleted=0 and eear.email_id not in\n	(select eb.email_id from emails_beans eb where eb.bean_module ='Accounts' and eb.bean_id = '')\n	) derivedemails on derivedemails.email_id = emails.id",
                          'join_tables' => array(''),
                    );

        $actual = $this->account->get_unlinked_email_query(array('return_as_array' => 'true'));
        $this->assertSame($expected, $actual);
    }

    public function testgetProductsServicesPurchasedQuery()
    {
        //without account id
        $expected = "\n			SELECT\n				aos_products_quotes.*\n			FROM\n				aos_products_quotes\n			JOIN aos_quotes ON aos_quotes.id = aos_products_quotes.parent_id AND aos_quotes.stage LIKE 'Closed Accepted' AND aos_quotes.deleted = 0 AND aos_products_quotes.deleted = 0\n			JOIN accounts ON accounts.id = aos_quotes.billing_account_id AND accounts.id = ''\n\n			";
        $actual = $this->account->getProductsServicesPurchasedQuery();
        $this->assertSame($expected, $actual);

        //with account id
        $expected = "\n			SELECT\n				aos_products_quotes.*\n			FROM\n				aos_products_quotes\n			JOIN aos_quotes ON aos_quotes.id = aos_products_quotes.parent_id AND aos_quotes.stage LIKE 'Closed Accepted' AND aos_quotes.deleted = 0 AND aos_products_quotes.deleted = 0\n			JOIN accounts ON accounts.id = aos_quotes.billing_account_id AND accounts.id = '1234'\n\n			";
        $this->account->id = '1234';
        $actual = $this->account->getProductsServicesPurchasedQuery();
        $this->assertSame($expected, $actual);
    }
}
