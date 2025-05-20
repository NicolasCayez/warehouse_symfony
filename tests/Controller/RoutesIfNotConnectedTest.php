<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RoutesIfNotConnectedTest extends WebTestCase
{
    public function testRoutesifNotConnected(): void
    {
        $user = new User;
        $client = static::createClient();

        //* HOME
        // Route 'home' if not connected
        // -> should be OK
        $client->request('GET', '/home');
        self::assertResponseIsSuccessful();

        //* WAREHOUSES
        // Route 'warehouses' if not connected
        // -> should redirect to route 'home'
        $client->request('GET', '/warehouses');
        self::assertResponseRedirects('');
        // Route 'warehouses_detail' if not connected
        // -> should redirect to route 'home'
        $client->request('GET', '/warehouses/2');
        self::assertResponseRedirects('');

        //* PRODUCTS
        // Route 'products' if not connected
        // -> should redirect to route 'home'
        $client->request('GET', '/products');
        self::assertResponseRedirects('');
        // Route 'products_filtered' if not connected
        // -> should redirect to route 'home'
        $client->request('GET', '/products/e');
        self::assertResponseRedirects('');

        //* RECEPTIONS
        // Route 'receptions' if not connected
        // -> should redirect to route 'home'
        $client->request('GET', '/receptions');
        self::assertResponseRedirects('');
        // Route 'receptions_filtered' if not connected
        // -> should redirect to route 'home'
        $client->request('GET', '/receptions/filtered/e');
        self::assertResponseRedirects('');
        // Route 'receptions_by_warehouse' if not connected
        // -> should redirect to route 'home'
        $client->request('GET', '/receptions/2');
        self::assertResponseRedirects('');
        // Route 'receptions_by_warehouse_filtered' if not connected
        // -> should redirect to route 'home'
        $client->request('GET', '/receptions/2/filtered/e');
        self::assertResponseRedirects('');
        // Route 'new_reception' if not connected
        // -> should redirect to route 'home'
        $client->request('GET', '/receptions/2/new');
        self::assertResponseRedirects('');
        // Route 'reception_detail' if not connected
        // -> should redirect to route 'home'
        $client->request('GET', '/receptions/2/2');
        self::assertResponseRedirects('');
        // Route 'delete_reception_movement' if not connected
        // -> should redirect to route 'home'
        $client->request('GET', '/receptions/2/2/remove/2');
        self::assertResponseRedirects('');
        // Route 'reception_detail_filtered' if not connected
        // -> should redirect to route 'home'
        $client->request('GET', '/receptions/2/2/filter/e');
        self::assertResponseRedirects('');

        //* STOCK TRANSFERTS
        // Route 'transferts' if not connected
        // -> should redirect to route 'home'
        $client->request('GET', '/transferts_list');
        self::assertResponseRedirects('');
        // Route 'transferts' filtered if not connected
        // -> should redirect to route 'home'
        $client->request('GET', '/transferts_list/e');
        self::assertResponseRedirects('');
        // Route 'transferts_by_warehouse' if not connected
        // -> should redirect to route 'home'
        // $client->request('GET', '/transferts/6/list');
        // self::assertResponseRedirects('');
        // Route 'transferts_by_warehouse_filtered' if not connected
        // -> should redirect to route 'home'
        // $client->request('GET', '/transferts/6/list/e');
        // self::assertResponseRedirects('');
        // Route 'new_transfert' if not connected
        // -> should redirect to route 'home'
        // $client->request('GET', '/transferts/4/new');
        // self::assertResponseRedirects('');
        // Route 'transfert_detail' if not connected
        // -> should redirect to route 'home'
        // $client->request('GET', '/transferts/6/2');
        // self::assertResponseRedirects('');
        // Route 'delete_transfert_movement' if not connected
        // -> should redirect to route 'home'
        // $client->request('GET', '/transferts/2/2/remove/2');
        // self::assertResponseRedirects('');
        // Route 'transfert_detail_filtered' if not connected
        // -> should redirect to route 'home'
        // $client->request('GET', '/transferts/6/2/filter/e');
        // self::assertResponseRedirects('');
    }
}
