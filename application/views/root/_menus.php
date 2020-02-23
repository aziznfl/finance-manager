  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        <?php 
          $activeLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
          $menus = $GLOBALS['menus'];
          for ($i = 0; $i < count($menus[0]); $i++) {
            $menu = $menus[0][$i];

            // set active for menu
            $link = base_url($menu['link']);
            $activeMenu = "";
            if ($activeLink == base_url() && $activeLink == $link) {
              $activeMenu = "active";
            } else if (strpos($activeLink, $link) !== false) {
              if ($link != base_url()) $activeMenu = "active";
            }

            // add submenus
            $submenus = "";
            $j = 0;
            while($j < count($menus[1])) {
              $sm = $menus[1][$j];
              if ($sm['parent_id'] == $menu['menu_id']) {
                $link = "#";
                $activeSubmenu = "";
                if (strpos($activeLink, base_url($sm['link'])) !== false) $activeSubmenu = "active";
                $submenus .= '<li class="'.$activeSubmenu.'"><a href="'.base_url($sm['link']).'"><i class="'.$sm['menu_icon'].'"></i> '.ucwords($sm['menu_name']).'</a></li>';
              }
              $j++;
            }

            // add treeview text and icon on the right menu
            $treeview = "";
            $treeviewIcon = "";
            if ($submenus != "") {
              $treeview = $activeMenu == "active" ? " " : "" . "treeview";
              $treeviewIcon = '
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>';
            }

            // print menus
            echo '<li class="'.$activeMenu, $treeview.'">
              <a href="'.$link.'">
                <i class="'.$menu['menu_icon'].'"></i> <span>'.ucwords($menu['menu_name']).'</span>
                '.$treeviewIcon.'
              </a>
              <ul class="treeview-menu">
                '.$submenus.'
              </ul>
            </li>';
          } 
        ?>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">