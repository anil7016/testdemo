<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/inventory/html/includes/config.php';

//require_once $_SERVER['DOCUMENT_ROOT'] . '/html/class/db.class.php';
require_once 'ssp.class.php';

require_once 'ssp2.class.php';

class datatable {

    protected $_db;
    protected $_sql_details = array(
        'user' => DB_USERNAME,
        'pass' => DB_PASSWORD,
        'db' => DB_DATABASE,
        'host' => DB_SERVER
    );

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function getUsers() {
        //$extraWhere = 'role_id !=1';
        $table = 'inv_users';
        $primaryKey = 'user_id';
        $columns = array(
            array('db' => 'first_name', 'dt' => 0, 'field' => 'first_name', 'searchable' => 'first_name'),
            array('db' => 'last_name', 'dt' => 1, 'field' => 'last_name', 'searchable' => 'last_name'),
            array('db' => 'email', 'dt' => 2, 'field' => 'email', 'searchable' => 'email'),
            array('db' => 'cell_number', 'dt' => 3, 'field' => 'cell_number', 'searchable' => 'cell_number'),
            array('db' => 'user_id', 'dt' => 4, 'field' => 'user_id', 'searchable' => 'user_id', 'formatter' => function( $d, $row ) {
            if ($row['is_active'] == 1) {
                return '<a href="users/edit/' . $d . '" class="btn btn-xs show-tooltip" title="" data-original-title="Edit"><span class="fa fa-edit"></span></a>' .
                        '<button onclick="user_active(this.id, 0)" id="' . $row['user_id'] . '" class="btn btn-xs show-tooltip" ><span class="fa fa-close"></span></button>';
            } else {
                return '<a href="users/edit/' . $d . '" class="btn btn-xs show-tooltip" title="" data-original-title="Edit"><span class="fa fa-edit"></span></a>' .
                        '<button onclick="user_active(this.id, 1)" id="' . $row['user_id'] . '" class="btn btn-xs show-tooltip" ><span class="fa fa-check-square-o"></span></button>';
            }
        }),
        );
        $sql = 'SELECT * FROM inv_users $where $order $limit';
        echo json_encode(
                SSP2::simple($_REQUEST, $this->_sql_details, $table, $primaryKey, $columns, $sql)
        );
    }

    public function getCategory() {
        $table = 'inv_category';
        $primaryKey = 'id';
        $columns = array(
            array('db' => 'name', 'dt' => 0, 'field' => 'category_name', 'searchable' => 'name'),
            array('db' => 'description', 'dt' => 1, 'field' => 'description', 'searchable' => 'description'),
            array('db' => 'id', 'dt' => 2, 'field' => 'action', 'formatter' => function( $d, $row ) {
            if ($row['is_active'] == 1) {
                return '<a href="category/edit/' . $d . '" class="btn btn-xs show-tooltip" title="" data-original-title="Edit"><span class="fa fa-edit"></span></a>'
                        . '<button onclick="active_deactive(this.id, 0)" id="' . $row['id'] . '" class="btn btn-xs show-tooltip" ><span class="fa fa-close"></span></button>';
            } else {
                return '<a href="category/edit/' . $d . '" class="btn btn-xs show-tooltip" title="" data-original-title="Edit"><span class="fa fa-edit"></span></a>'
                        . '<button onclick="active_deactive(this.id, 1)" id="' . $row['id'] . '" class="btn btn-xs show-tooltip" ><span class="fa fa-check-square-o"></span></button>';
            }
        })
        );
        $sql = 'SELECT * FROM inv_category $where $order $limit';
        echo json_encode(
                SSP2::simple($_REQUEST, $this->_sql_details, $table, $primaryKey, $columns, $sql)
        );
    }

    public function getContact($type = "") {

        if (isset($type) && $type != "") {
            $extraWhere = 'type =' . $type;
        } else {
            $extraWhere = '';
        }
        $table = 'inv_contact';
        $primaryKey = 'id';
        $columns = array(
            array('db' => 'name', 'dt' => 0, 'field' => 'name', 'searchable' => 'name', 'formatter' => function( $d, $row ) {
            return '<a href="contact/item/' . $row['id'] . '" style="color:blue;">' . $d . '</a>';
        }),
            array('db' => 'type', 'dt' => 1, 'field' => 'type', 'formatter' => function( $d, $row ) {
            $a = $d == 1 ? 'Client' : 'Supplier';
            return $a;
        }),
            array('db' => 'phone', 'dt' => 2, 'field' => 'phone', 'searchable' => 'phone'),
            array('db' => 'email', 'dt' => 3, 'field' => 'email', 'searchable' => 'email'),
            array('db' => 'country', 'dt' => 4, 'field' => 'country', 'searchable' => 'country'),
            array('db' => 'id', 'dt' => 5, 'field' => 'action', 'formatter' => function( $d, $row ) {
            return '<a href="contact/edit/' . $d . '" class="btn btn-xs show-tooltip" title="" data-original-title="Edit"><span class="fa fa-edit"></span></a>';
        })
        );
        $joinQuery = 'SELECT * FROM inv_contact $where $order $limit';
        echo json_encode(
                SSP::simple1($_REQUEST, $this->_sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
        );
    }

    public function getTransfer() {
        $table = 'inv_masters';
        $primaryKey = 'id';
        $extraWhere = 'type = 4';
        $columns = array(
            array('db' => 'sr_number', 'dt' => 0, 'field' => 'st_number', 'searchable' => 'st_number', 'formatter' => function( $d, $row ) {
            return '<a href="stock_transfer/edit/' . $row['id'] . '" style="color:blue;">' . $d . '</a>';
        }),
            array('db' => 'date', 'dt' => 1, 'field' => 'date', 'searchable' => 'date',)
        );
        $sql = 'SELECT * FROM inv_masters $where $order $limit';
        echo json_encode(
                SSP2::simple($_REQUEST, $this->_sql_details, $table, $primaryKey, $columns, $sql, $extraWhere)
        );
    }

    public function getOrder($type = "") {
        $extraWhere = 'o.type = 2';
        if (isset($type) && $type != "") {
            $extraWhere .= ' AND o.status =' . $type;
        } else {
            $extraWhere .= '';
        }

        $table = 'inv_masters';
        $primaryKey = 'id';
        $columns = array(
            array('db' => 'sr_number', 'dt' => 0, 'field' => 'order_number', 'searchable' => 'o.order_number', 'formatter' => function( $d, $row ) {
            return '<a href="order/view/' . $row['id'] . '" style="color:blue;">' . $d . ' </a>';
        }),
            array('db' => 'date', 'dt' => 1, 'field' => 'date', 'searchable' => 'o.date'),
            array('db' => 'delivery_date', 'dt' => 2, 'field' => 'delivery_date', 'searchable' => 'o.delivery_date'),
            array('db' => 'contact', 'dt' => 3, 'field' => 'contact', 'searchable' => 'c.name'),
            array('db' => 'reference', 'dt' => 4, 'field' => 'client_reference', 'searchable' => 'o.client_reference'),
            array('db' => 'status', 'dt' => 5, 'field' => 'action', 'formatter' => function( $d, $row ) {
            if ($d == 0) {
                return 'Pending';
            } else {
                return 'Completed';
            };
        })
        );
        $joinQuery = 'SELECT o.id,o.sr_number ,o.date ,o.delivery_date,o.reference,o.status,c.name as contact FROM inv_masters as o JOIN inv_contact as c ON o.contact = c.id $where $order $limit';
        //echo json_encode($_REQUEST);
        echo json_encode(
                SSP2::simple($_REQUEST, $this->_sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
        );
    }

    public function getShipment($type = "") {
        $table = 'inv_masters';
        $primaryKey = 'id';
        $extraWhere = 'o.type = 3';
        $columns = array(
            array('db' => 'sr_number', 'dt' => 0, 'field' => 'sh_number', 'searchable' => 's.sh_number', 'formatter' => function( $d, $row ) {
            return '<a href="shipment/view/' . $row['id'] . '" style="color:blue;">' . $d . '</a>';
        }),
            array('db' => 'date', 'dt' => 1, 'field' => 'date', 'searchable' => 'o.date'),
//            array('db' => 'delivery_date', 'dt' => 2, 'field' => 'delivery_date', 'searchable' => 'delivery_date'),
            array('db' => 'contact', 'dt' => 2, 'field' => 'contact', 'searchable' => 'c.name'),
            array('db' => 'reference', 'dt' => 3, 'field' => 'invoice_reference', 'searchable' => 'o.invoice_reference'),
            array('db' => 'status', 'dt' => 4, 'field' => 'action', 'formatter' => function( $d, $row ) {
            if ($d == 0) {
                return 'Pending';
            } else {
                return 'Completed';
            };
        })
        );

        $sql = 'SELECT o.sr_number,o.id,o.date,c.name as contact, o.reference, o.status FROM inv_masters as o JOIN inv_contact as c ON o.contact = c.id $where $order $limit';
        echo json_encode(
                SSP2::simple($_REQUEST, $this->_sql_details, $table, $primaryKey, $columns, $sql, $extraWhere)
        );
    }

    public function getInventory($type = "") {
        $extraWhere = (isset($type) && $type != "") ? 'cat.id = ' . $type : '';
        $table = 'inv_item';
        $primaryKey = 'id';
        $columns = array(
            array('db' => 'item_code', 'dt' => 0, 'field' => 'item_code', 'searchable' => 'item.item_code', 'formatter' => function($d, $row) {
            return '<a href="order/item/' . $row['id'] . '" style="color:blue;">' . $d . ' </a>';
        }),
            array('db' => 'item_name', 'dt' => 1, 'field' => 'item_name', 'searchable' => 'item.item_name'),
            array('db' => 'size', 'dt' => 2, 'field' => 'size', 'searchable' => 'item.size'),
            array('db' => 'category', 'dt' => 3, 'field' => 'category', 'searchable' => 'cat.name'),
            array('db' => 'on_order', 'dt' => 4, 'field' => 'on_order', 'searchable' => 'item.on_order'),
            array('db' => 'in_stock_ar', 'dt' => 5, 'field' => 'in_stock_ar', 'searchable' => 'item.in_stock_ar'),
            array('db' => 'in_stock_pd', 'dt' => 6, 'field' => 'in_stock_pd', 'searchable' => 'item.in_stock_pd'),
            array('db' => 'total_in_stock', 'dt' => 7, 'field' => 'total_in_stock', 'searchable' => 'item.total_in_stock'),
            array('db' => 'item_code', 'dt' => 8, 'field' => 'pending', 'formatter' => function( $d, $row ) {
            if ($row['on_order'] - $row['total_in_stock'] >= 0) {
                return $row['on_order'] - $row['total_in_stock'];
            } else {
                return '-';
            }
        }),
            array('db' => 'item_code', 'dt' => 9, 'field' => 'over_stock', 'formatter' => function( $d, $row ) {
            if ($row['on_order'] - $row['total_in_stock'] >= 0) {
                return '-';
            } else {
                return $row['total_in_stock'] - $row['on_order'];
            }
        })
        );

        $joinQuery = 'SELECT item.id,item.item_code,item.on_order,item.in_stock_ar,item.in_stock_pd, item.total_in_stock ,item.item_name ,item.size,cat.name as category FROM inv_item as item JOIN inv_category as cat ON item.category = cat.id $where $order $limit';

        echo json_encode(
                SSP2::simple($_REQUEST, $this->_sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
        );
    }

    public function getItem() {
        $table = 'inv_item';
        $primaryKey = 'id';
        $columns = array(
            array('db' => 'item_code', 'dt' => 0, 'field' => 'item_code', 'searchable' => 'item.item_code', 'formatter' => function( $d, $row ) {
            return '<a href="order/item/' . $row['id'] . '" style="color:blue;">' . $d . ' </a>';
        }),
            array('db' => 'item_name', 'dt' => 1, 'field' => 'item_name', 'searchable' => 'item.item_name'),
            array('db' => 'size', 'dt' => 2, 'field' => 'size', 'searchable' => 'item.size'),
            array('db' => 'category', 'dt' => 3, 'field' => 'category', 'searchable' => 'cat.name'),
            array('db' => 'id', 'dt' => 4, 'field' => 'action', 'formatter' => function( $d, $row ) {
            return '<a href="item_master/edit/' . $d . '" class="btn btn-xs show-tooltip" title="" data-original-title="Edit"><span class="fa fa-edit"></span></a>';
        })
        );
        $joinQuery = 'SELECT item.id,item.item_code ,item.item_name ,item.size,cat.name as category FROM inv_item as item JOIN inv_category as cat ON item.category = cat.id $where $order $limit';

        echo json_encode(
                SSP2::simple($_REQUEST, $this->_sql_details, $table, $primaryKey, $columns, $joinQuery)
        );
    }

    public function getBatch($type = "") {
        $extraWhere = 'r.type=1';
        $extraWhere .= (isset($type) && $type != "") ? 'AND cat.id = ' . $type : '';
        $table = 'inv_masters';
        $primaryKey = 'id';
        $columns = array(
            array('db' => 'sr_number', 'dt' => 0, 'field' => 'batch_number', 'searchable' => 'r.batch_number', 'formatter' => function( $d, $row ) {
            return '<a href="inventory_batch/view/' . $row['id'] . '" style="color:blue;">' . $d . ' </a>';
        }),
            array('db' => 'date', 'dt' => 1, 'field' => 'date', 'searchable' => 'r.date'),
            array('db' => 'contact', 'dt' => 2, 'field' => 'contact_id', 'searchable' => 'c.name'),
            array('db' => 'reference', 'dt' => 3, 'field' => 'supplier_reference', 'searchable' => 'r.supplier_reference')
        );
        $joinQuery = 'SELECT r.id, r.sr_number, r.date, c.name as contact, r.reference  FROM inv_masters as r JOIN inv_contact as c ON c.id = r.contact $where $order $limit';

        echo json_encode(
                SSP2::simple($_REQUEST, $this->_sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
        );
    }

    public function getValuation($type = "", $date = "") {
        if (isset($date) && $date != "") {
            $extraWhere = 'ih.date = "' . $date . '"';
        } else {
            $extraWhere = 'ih.date = "' . date('Y-m-d') . '"';
        }

        if (isset($type) && $type != "") {
            $extraWhere .= ' AND cat.id = ' . $type;
        }
        $extraWhere.=' GROUP BY ih.item_id';


        $table = 'inv_item_history';
        $primaryKey = 'id';
        $columns = array(
            array('db' => 'item_code', 'dt' => 0, 'field' => 'item_code', 'searchable' => 'item.item_code', 'formatter' => function( $d, $row ) {
            return '<a href="order/item/' . $row['item_id'] . '" style="color:blue;">' . $d . ' </a>';
        }),
            array('db' => 'item_name', 'dt' => 1, 'field' => 'item_name', 'searchable' => 'item.item_name'),
            array('db' => 'size', 'dt' => 2, 'field' => 'size', 'searchable' => 'item.size'),
            array('db' => 'category', 'dt' => 3, 'field' => 'category', 'searchable' => 'cat.name'),
            array('db' => 'total_in_stock', 'dt' => 4, 'field' => 'total_in_stock', 'searchable' => 'ih.total_in_stock'),
            array('db' => 'cost_per_unit', 'dt' => 5, 'field' => 'cost_per_unit', 'searchable' => 'ih.cost_per_unit'),
            array('db' => 'id', 'dt' => 6, 'field' => 'total', 'formatter' => function( $d, $row ) {
            return '$' . $row['total_in_stock'] * $row['cost_per_unit'];
        }),
        );
        //$joinQuery = 'SELECT item.id,item.item_code,item.total_in_stock,item.cost_per_unit, item.item_name,item.size,cat.id as cat_id,cat.name as category, ih.modified_date as date FROM inv_item as item JOIN inv_category as cat ON item.category = cat.id LEFT JOIN inv_item_history as ih ON item.item_id = ih.item_id $where $order $limit';
        $joinQuery = 'SELECT ih.*, item.item_code,item.item_name, item.size, cat.name as category, cat.id  FROM (SELECT * FROM `inv_item_history` ORDER BY `inv_item_history`.`time` DESC) AS ih LEFT JOIN inv_item as item ON item.id = ih.item_id LEFT JOIN inv_category as cat ON cat.id = item.category  $where $order $limit';

        echo json_encode(
                SSP2::simple($_REQUEST, $this->_sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
        );
    }

}
