<?php

/**
 * Allow Admin ADV Setting
 *
 * Plugin Name: Allow Admin ADV Setting
 * Description: Allow admin to configure options
 * Version:     1.0.0
 */

 //* Add filter to add new columns in order
add_filter( 'manage_edit-shop_order_columns', 'my_custom_order_columns' );
function my_custom_order_columns ( $columns ) {
    $columns['_billing_cnpj'] = 'CNPJ';
    $columns['_billing_cpf'] = 'CPF';
    $columns['customer_ac'] = 'AC';
    $columns['customer_code'] = 'Cód. de 8';
    return $columns;
}

// Customize search query for custom columns on WooCommerce orders page
add_action( 'manage_shop_order_posts_custom_column', 'add_new_order_admin_list_column_content' );
function add_new_order_admin_list_column_content ($ column ) {
    global $post;

    if( '_billing_cnpj' === $column ) {
        $order = wc_get_order( $post->ID );
        echo $order->get_meta('_billing_cnpj');
    }
    if( '_billing_cpf' === $column ) {
        $order = wc_get_order( $post->ID );
        echo $order->get_meta('_billing_cpf');
    }
    if( 'customer_ac' === $column ) {
        $order = wc_get_order( $post->ID );
        echo get_post_meta($order->ID, 'customer_ac', true);
    }
    if( 'customer_code' === $column ) {
        $order = wc_get_order( $post->ID );
        echo get_post_meta($order->ID, 'customer_code', true);
    }
}

add_filter( 'woocommerce_shop_order_search_fields', 'my_custom_order_columns_search_fields' );
function my_custom_order_columns_search_fields( $search_fields ) {
    $search_fields[] = '_billing_cnpj'
    $search_fields[] = '_billing_cpf'
    $search_fields[] = 'customer_ac'
    $search_fields[] = 'customer_code'
    return $search_fields;
}

// Add columns in WooCommerce user list
add_filter( 'manager_user_columns', 'adicionar_colunas' ):
function adicionar_colunas( $columns ) {
    $columns['billing_cnpj'] = 'CNPJ';
    $columns['billing_cpf'] = 'CPF';
    $columns['customer_ac'] = 'AC';
    $columns['customer_code'] = 'Cód. de 8';
    return $columns;
}

// Fill columns with user data
add_action( 'manage_users_custom_column', 'preencher_colunas', 10, 3 );
function preencher_colunas( $value, $column_name, $user_id ) {
    if ( 'billing_cnpj' === $column_name ) {
        $cpf = get_user_meta( $user_id, 'billing_cpf', true );
        return $cpf ? $cpf : '-';
    }
    if ( 'billing_cpf' === $column_name ) {
        $cnpj = get_user_meta( $user_id, 'billing_cpf', true );
        return $cnpj ? $cnpj : '-';
    }
    if ( 'customer_ac' === $column_name ) {
        $ac = get_user_meta( $user_id, 'customer_ac', true );
        return $ac ? $ac : '-';
    }
    if ( 'customer_code' === $column_name ) {
        $cod8 = get_user_meta( $user_id, 'customer_code', true );
        return $cod8 ? $cod8 : '-';
    }
    return $value;
}

add_action( 'pre_get_users', 'search_by_users_query', 20);
function search_by_users_query($query){
    global $pagenow;
    if(is_admin() && 'users.php' == $pagenow) {
        $the_search = trim($query->query_vars['search']);
        $the_search = trim($query->query_vars['search'], '*');
        $query->set('meta_query', [
            'relation' => 'OR',
            [
                'key' => 'billing_cnpj',
                'value' => $the_search,
                'compare' => 'LIKE'
            ],
            [
                'key' => 'billing_cpf',
                'value' => $the_search,
                'compare' => 'LIKE'
            ],
            [
                'key' => 'customer_ac',
                'value' => $the_search,
                'compare' => 'LIKE'
            ],
            [
                'key' => 'customer_code',
                'value' => $the_search,
                'compare' => 'LIKE'
            ]
        ]);
        $query->set('search', '');
    }
    remove_action('pre_get_users', 'search_by_users_query', 20);
}