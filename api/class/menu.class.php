<?php

class menu {

    protected $_db;

    public function __construct() {
        $this->_db = db::getInstance();
        //print_r($_SESSION['user_data']);
    }

    //get menu
    public function menu($menu_alias, $field_name = '') {
        $this->_db->where('menu_alias', $menu_alias);
        $data = $this->_db->get(TBL_MENUS);
        //$menu_alias_query = $this->_db->selectdata(TBL_MENUS, "menu_alias = '" . $menu_alias . "'");
        // $data = mysql_fetch_assoc($menu_alias_query);
        if ($field_name == "") {
            return $data;
        } else {
            $field_names = explode(',', $field_name);
            $return_data = "";
            foreach ($field_names as $field) {
                $return_data.= $data[0][$field] . ' ';
            }
            return trim($return_data);
        }
    }

    //get menu
    public function menu_row($menu_id, $field_name = '') {
        $this->_db->where('menu_id', $menu_id);
        $data = $this->_db->get(TBL_MENUS);
        //$menu_alias_query = $this->_db->selectdata(TBL_MENUS, "menu_alias = '" . $menu_alias . "'");
        // $data = mysql_fetch_assoc($menu_alias_query);
        if ($field_name == "") {
            return $data;
        } else {
            $field_names = explode(',', $field_name);
            $return_data = "";
            foreach ($field_names as $field) {
                $return_data.= $data[0][$field] . ' ';
            }
            return trim($return_data);
        }
    }

    public function copy_roles_to_menu_access() {
        $roles = $this->_db->get(TBL_ROLES, null, 'role_id');
        for ($a = 0; $a < sizeof($roles); $a++) {
            $this->_db->where('menu_access_role_id', $roles[$a]['role_id']);
            $this->_db->get(TBL_MENU_ACCESS);
            if ($this->_db->count == 0) {
                $data = array("menu_access_status" => "1",
                    "menu_access_created_by" => "" . $_SESSION['user_data'][0]['user_id'] . "",
                    "menu_access_modified_by" => "" . $_SESSION['user_data'][0]['user_id'] . "",
                    "menu_access_created_date" => date('Y-m-d'),
                    "menu_access_modified_date" => date('Y-m-d'),
                    "menu_access_menu_ids" => '3,8',
                    "menu_access_role_id" => $roles[$a]['role_id']);
                $this->_db->insert(TBL_MENU_ACCESS, $data);
            }
        }
        return;
    }

    public function user_menu_access() {

        $this->copy_roles_to_menu_access();
        $this->_db->orderBy('menu_access_role_id', "asc");
        $this->_db->join(TBL_ROLES, TBL_MENU_ACCESS . ".menu_access_role_id = " . TBL_ROLES . ".role_id", "RIGHT");
        $menu_access = $this->_db->get(TBL_MENU_ACCESS);
        return $menu_access;
    }

    //get menu title
    public function icon($menu_alias) {
        $menu_alias_query = $this->_db->selectdata(TBL_MENUS, "menu_alias = '" . $menu_alias . "'");
        $menu_alias = mysql_fetch_object($menu_alias_query);
        return $menu_alias->menu_alias;
    }

    //check menu access
    public function check_menu_access($menu_alias, $role_id) {
        
    }

    //get menu file and check menu access
    public function get_menu_file($menu_alias, $role_id) {

        $this->_db->where('menu_alias', $menu_alias);
        $menu_alias = $this->_db->get(TBL_MENUS);

        //$menu_alias_query = $this->_db->selectdata(TBL_MENUS, "menu_alias = '" . $menu_alias . "'");
        //$menu_alias = mysql_fetch_object($menu_alias_query);
        $_SESSION['menu_id'] = $menu_alias[0]['menu_id'];
        //check menu_access
        $menu_access = $this->_db->rawQuery("SELECT * FROM " . TBL_MENU_ACCESS . " WHERE menu_access_role_id = '" . $role_id . "' AND FIND_IN_SET('" . $menu_alias[0]['menu_id'] . "', menu_access_menu_ids)");
        //$menu_access = mysql_num_rows($menu_access_query);

        if ($this->_db->count > 0) {
            return $menu_alias[0]['menu_url'];
        } else {
            return FALSE;
        }
    }

//get all the menu
    public function get_menu($role_id, $level, $parent, $class, $menu_type) {
        $lang = new en();
        $user_types = explode(',', $role_id);
        $this->_db->join(TBL_MENUS, "FIND_IN_SET(menu_id, menu_access_menu_ids)", "LEFT");

        for ($u = 0; $u < sizeof($user_types); $u++) {
            $this->_db->where("menu_access_role_id", $user_types[$u]);
        }

        $this->_db->where("menu_level", $level);
        $this->_db->where("menu_parent_id", $parent);
        $this->_db->where("menu_status", 1);
        $this->_db->where("menu_type", $menu_type);
        $this->_db->orderBy("menu_order, menu_id", "asc");
        $menu = $this->_db->get(TBL_MENU_ACCESS, null);
        ?>

        <ul class="<?php echo $menu_type == 'top' && $level > 1 ? 'nav nav-main' : $class; ?>">
        <?php
        for ($m = 0; $m < sizeof($menu); $m++) {

            $this->_db->where('menu_level', ($menu[$m]['menu_level'] + 1));
            $this->_db->where('menu_parent_id', $menu[$m]['menu_id']);
            $this->_db->where('menu_status', 1);
            $submenu = $this->_db->get(TBL_MENUS);
            $submenu_num_row = $this->_db->count;
            echo ($level == 2) ? '<span class="arrow top"></span>' : '';
            ?>
                <!-- listing of menu -->
                <li class="<?php echo $menu[$m]['menu_alias'] == $_REQUEST['page'] ? ' nav-active' : "" ?>">
                    <!-- menu title, menu description goew here -->

                    <a href="<?php
            if ($submenu_num_row > 0) {
                echo '#';
            } else {
                echo $menu[$m]['menu_alias'];
            }
            if ($level == 1) {
                $icon_cass = "sidebar-icon " . $menu[$m]['menu_icon_class'];
            } else {
                $icon_cass = $menu[$m]['menu_icon_class'];
            }
            ?>" class="<?php
                    if ($submenu_num_row > 0 && $level == 1) {
                        echo 'dropdown-toggle" data-toggle="dropdown';
                    } else {
                        echo $menu[$m]['menu_alias'] == $_REQUEST['page'] ? 'active dker' : "";
                    }
                    ?>">
                        <i class="<?php echo $menu[$m]['menu_icon_class']; ?>" aria-hidden="true"></i>
                        <span><?php echo $menu[$m]['menu_name']; ?></span>

                    </a>
            <?php
            if ($submenu_num_row > 0) {
                ?>
                        <?php echo $this->get_menu($role_id, $level + 1, $menu[$m]['menu_id'], 'dropdown-menu animated fadeInRight', $menu_type); ?>
                    <?php } ?>
                </li>
                    <?php
                }
                ?>



        </ul>


        <?php
    }

//get list of access menu
    public function menus($level, $parent, $user_id, $dashboard_ids = '') {
        //include '../language/en.php';
        $lang = new en();

        $this->_db->where('menu_level', $level);
        $this->_db->where('menu_parent_id', $parent);
        $this->_db->where('menu_status', 1);
        $this->_db->where('menu_id != 40');

        $menu = $this->_db->get(TBL_MENUS);
        $this->_db->where('menu_access_role_id', $user_id);
        $user_menu_id = $this->_db->get(TBL_MENU_ACCESS);
        ?>
        <!-- display menu with checkbox and their name in tabular format -->

        <?php
        for ($m = 0; $m < sizeof($menu); $m++) {
            ?>
            <!-- checkbox array to display with menu -->
            <div class="controls">
                <label class="checkbox" for="menu_<?php echo $menu[$m]['menu_id']; ?>">
                    <input type="checkbox"
            <?php
            //to check which menu is already assigned to user type group when update button clicked
            $menus = explode(",", @$user_menu_id[0]['menu_access_menu_ids']);

            foreach ($menus as $b) {
                if ($b == $menu[$m]['menu_id']) {
                    ?>
                                   checked="checked"
                            <?php
                        }
                    }
                    //  print_r($_SESSION['user_data']); 
                    ?>
                           class="checkbox" <?php if ($menu[$m]['menu_id'] == '9') { ?> onclick="fnchecked(this.checked, 'show_rights', '1');" <?php } ?> name="menu_access_menu_ids[]" id="menu_<?php echo $menu[$m]['menu_id']; ?>" value="<?php echo $menu[$m]['menu_id']; ?>" > <?php echo "" . $lang->lang[$menu[$m]['menu_alias']]; ?>
                </label>
            </div>
            <div class="controls" style="padding-left:20px;">
            <?php
            //menu name
            $this->_db->where('menu_level', ($level + 1));
            $this->_db->where('menu_parent_id', $menu[$m]['menu_id']);
            $this->_db->where('menu_status', 1);
            $this->_db->get(TBL_MENUS);
            $num_rows = $this->_db->count;
            if ($num_rows > 0) {
                $level++;
                $this->menus($level, $menu[$m]['menu_id'], $user_id);
            } else {
                $this->menus($level, $menu[$m]['menu_id'], $user_id);
            }
            ?>
            </div>
                <?php
            }
        }

        public function breadcrumbs($text = 'You are here: ', $sep = '', $home = 'Home') {
            //<ul class="breadcrumb">
            //       <li class="active"><i class="icon-home"></i> Home</li>
            //   </ul>
            //Use RDFa breadcrumb, can also be used for microformats etc.
            $bc = '<ul class="breadcrumb default square rsaquo sm"">';
            //Get the website:
            $site = 'http://' . $_SERVER['HTTP_HOST'];
            //Get all vars en skip the empty ones
            $crumbs = array_filter(explode("/", $_SERVER["REQUEST_URI"]));
            //Create the home breadcrumb
            //$bc .= '<span typeof="v:Breadcrumb"><a href="' . $site . '" rel="v:url" property="v:title">' . $home . '</a>' . $sep . '</span>';
            //Count all not empty breadcrumbs
            $nm = count($crumbs);
            $i = 1;
            //Loop the crumbs
            foreach ($crumbs as $crumb) {
                //Make the link look nice
                $link = ucfirst(str_replace(array(".php", "-", "_"), array("", " ", " "), $crumb));
                //check second level menu
                if ($crumb == "index.php") {
                    $link = "Dashboard";
                }
                $this->_db->where('menu_alias', $crumb);
                $bd_second_level = $this->_db->get(TBL_MENUS);
                // echo $bd_second_level['menu_level'];
//            $bd_Condition = "menu_alias = '" . $crumb . "'";
//            $bd_second_level_query = $this->_db->selectdata(TBL_MENUS, $bd_Condition);
                //$bd_second_level = mysql_fetch_object($bd_second_level_query);

                if (@$bd_second_level['menu_level'] == '0') {
                    //echo $bd_parent->menu_name;
                    $bc .= '<li class="active"><a href="' . @$bd_second_level['menu_alias'] . '" rel="v:url" property="v:title"> ' . @$bd_second_level['menu_name'] . '</a>' . $sep . '</li>';
                }
                if (@ $bd_second_level[0]['menu_level'] > '1') {
                    $this->_db->where('menu_id', $bd_second_level[0]['menu_parent_id']);
                    $bd_parent = $this->_db->get(TBL_MENUS);
//                $bd_parent_Condition = "menu_id = '" . $bd_second_level[0]['menu_parent_id'] . "'";
//                $bd_parent_query = $this->_db->selectdata(TBL_MENUS, $bd_parent_Condition);
//                $bd_parent = mysql_fetch_object($bd_parent_query);
                    //echo $bd_parent->menu_name;
                    $bc .= '<li class="active"><a href="' . $bd_parent[0]['menu_alias'] . '" rel="v:url" property="v:title"> ' . $bd_parent[0]['menu_name'] . '</a>' . $sep . '</li>';
                }
                //Loose the last seperator
                $sep = $i == $nm ? '' : $sep;
                //Add crumbs to the root
                $site .= '/' . $crumb;
                //Make the next crumb

                if (@$bd_second_level[0]['menu_name'] == "") {
                    if (strpos($link, '&') !== false) {
                        $exp_link = explode('&', $link);
                        $new_link = $exp_link[0];
                    } else {
                        $new_link = $link;
                    }
                } else {
                    $new_link = $bd_second_level[0]['menu_name'];
                }
//            if ($new_link == 'palmetto' OR $new_link == 'Palmetto') {
//                $new_link = '<li class="active"><i class="fa fa-home"></i>' . $home . ' </li>';
//            }

                $bc .= '<li><a href="' . $site . '" rel="v:url" property="v:title">' . $new_link . ' </a> ' . $sep . '</li>';
                $i++;
            }
            $bc .= '</ul>';
            //Return the result
            return $bc;
        }

    }
    