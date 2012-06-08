<?php

include 'ajaxMain.php';
require_once PATH . 'config/configFormManager.php';

class _tree_struct {

    // Structure table and fields
    protected $table = "";
    protected $fields = array(
        "id" => false,
        "parent_id" => false,
        "position" => false,
        "left" => false,
        "right" => false,
        "level" => false
    );

    // Constructor
    function __construct($table = "tree", $fields = array()) {
        $this->table = $table;
        if (!count($fields)) {
            foreach ($this->fields as $k => &$v) {
                $v = $k;
            }
        } else {
            foreach ($fields as $key => $field) {
                switch ($key) {
                    case "id":
                    case "parent_id":
                    case "position":
                    case "left":
                    case "right":
                    case "level":
                        $this->fields[$key] = $field;
                        break;
                }
            }
        }
    }

    public function setDb($db) {
        $this->db = $db;
    }

    function _get_node($id) {
        /*
          $this->db->query("SELECT `" . implode("` , `", $this->fields) . "` FROM `" . $this->table . "` WHERE `" . $this->fields["id"] . "` = " . (int) $id);
          $this->db->nextr();
          return $this->db->nf() === 0 ? false : $this->db->get_row("assoc"); */
    }

    function _get_children($id, $recursive = false) {



        /*
          $children = array();
          if ($recursive) {
          $node = $this->_get_node($id);
          $this->db->query("SELECT `" . implode("` , `", $this->fields) . "` FROM `" . $this->table . "` WHERE `" . $this->fields["left"] . "` >= " . (int) $node[$this->fields["left"]] . " AND `" . $this->fields["right"] . "` <= " . (int) $node[$this->fields["right"]] . " ORDER BY `" . $this->fields["left"] . "` ASC");
          } else {
          $this->db->query("SELECT `" . implode("` , `", $this->fields) . "` FROM `" . $this->table . "` WHERE `" . $this->fields["parent_id"] . "` = " . (int) $id . " ORDER BY `" . $this->fields["position"] . "` ASC");
          }
          while ($this->db->nextr())
          $children[$this->db->f($this->fields["id"])] = $this->db->get_row("assoc");
          return $children; */
    }

    function _get_path($id) {/*
      $node = $this->_get_node($id);
      $path = array();
      if (!$node === false)
      return false;
      $this->db->query("SELECT `" . implode("` , `", $this->fields) . "` FROM `" . $this->table . "` WHERE `" . $this->fields["left"] . "` <= " . (int) $node[$this->fields["left"]] . " AND `" . $this->fields["right"] . "` >= " . (int) $node[$this->fields["right"]]);
      while ($this->db->nextr())
      $path[$this->db->f($this->fields["id"])] = $this->db->get_row("assoc");
      return $path; */
    }

    function _create($parent, $position) {
        // return $this->_move(0, $parent, $position);
    }

    function _remove($id) {
        /* if ((int) $id === 1) {
          return false;
          }
          $data = $this->_get_node($id);
          $lft = (int) $data[$this->fields["left"]];
          $rgt = (int) $data[$this->fields["right"]];
          $dif = $rgt - $lft + 1;

          // deleting node and its children
          $this->db->query("" .
          "DELETE FROM `" . $this->table . "` " .
          "WHERE `" . $this->fields["left"] . "` >= " . $lft . " AND `" . $this->fields["right"] . "` <= " . $rgt
          );
          // shift left indexes of nodes right of the node
          $this->db->query("" .
          "UPDATE `" . $this->table . "` " .
          "SET `" . $this->fields["left"] . "` = `" . $this->fields["left"] . "` - " . $dif . " " .
          "WHERE `" . $this->fields["left"] . "` > " . $rgt
          );
          // shift right indexes of nodes right of the node and the node's parents
          $this->db->query("" .
          "UPDATE `" . $this->table . "` " .
          "SET `" . $this->fields["right"] . "` = `" . $this->fields["right"] . "` - " . $dif . " " .
          "WHERE `" . $this->fields["right"] . "` > " . $lft
          );

          $pid = (int) $data[$this->fields["parent_id"]];
          $pos = (int) $data[$this->fields["position"]];

          // Update position of siblings below the deleted node
          $this->db->query("" .
          "UPDATE `" . $this->table . "` " .
          "SET `" . $this->fields["position"] . "` = `" . $this->fields["position"] . "` - 1 " .
          "WHERE `" . $this->fields["parent_id"] . "` = " . $pid . " AND `" . $this->fields["position"] . "` > " . $pos
          );
          return true; */
    }

    function _move($id, $ref_id, $position = 0, $is_copy = false) {
        /*  if ((int) $ref_id === 0 || (int) $id === 1) {
          return false;
          }
          $sql = array();      // Queries executed at the end
          $node = $this->_get_node($id);  // Node data
          $nchildren = $this->_get_children($id); // Node children
          $ref_node = $this->_get_node($ref_id); // Ref node data
          $rchildren = $this->_get_children($ref_id); // Ref node children

          $ndif = 2;
          $node_ids = array(-1);
          if ($node !== false) {
          $node_ids = array_keys($this->_get_children($id, true));
          // TODO: should be !$is_copy && , but if copied to self - screws some right indexes
          if (in_array($ref_id, $node_ids))
          return false;
          $ndif = $node[$this->fields["right"]] - $node[$this->fields["left"]] + 1;
          }
          if ($position >= count($rchildren)) {
          $position = count($rchildren);
          }

          // Not creating or copying - old parent is cleaned
          if ($node !== false && $is_copy == false) {
          $sql[] = "" .
          "UPDATE `" . $this->table . "` " .
          "SET `" . $this->fields["position"] . "` = `" . $this->fields["position"] . "` - 1 " .
          "WHERE " .
          "`" . $this->fields["parent_id"] . "` = " . $node[$this->fields["parent_id"]] . " AND " .
          "`" . $this->fields["position"] . "` > " . $node[$this->fields["position"]];
          $sql[] = "" .
          "UPDATE `" . $this->table . "` " .
          "SET `" . $this->fields["left"] . "` = `" . $this->fields["left"] . "` - " . $ndif . " " .
          "WHERE `" . $this->fields["left"] . "` > " . $node[$this->fields["right"]];
          $sql[] = "" .
          "UPDATE `" . $this->table . "` " .
          "SET `" . $this->fields["right"] . "` = `" . $this->fields["right"] . "` - " . $ndif . " " .
          "WHERE " .
          "`" . $this->fields["right"] . "` > " . $node[$this->fields["left"]] . " AND " .
          "`" . $this->fields["id"] . "` NOT IN (" . implode(",", $node_ids) . ") ";
          }
          // Preparing new parent
          $sql[] = "" .
          "UPDATE `" . $this->table . "` " .
          "SET `" . $this->fields["position"] . "` = `" . $this->fields["position"] . "` + 1 " .
          "WHERE " .
          "`" . $this->fields["parent_id"] . "` = " . $ref_id . " AND " .
          "`" . $this->fields["position"] . "` >= " . $position . " " .
          ( $is_copy ? "" : " AND `" . $this->fields["id"] . "` NOT IN (" . implode(",", $node_ids) . ") ");

          $ref_ind = $ref_id === 0 ? (int) $rchildren[count($rchildren) - 1][$this->fields["right"]] + 1 : (int) $ref_node[$this->fields["right"]];
          $ref_ind = max($ref_ind, 1);

          $self = ($node !== false && !$is_copy && (int) $node[$this->fields["parent_id"]] == $ref_id && $position > $node[$this->fields["position"]]) ? 1 : 0;
          foreach ($rchildren as $k => $v) {
          if ($v[$this->fields["position"]] - $self == $position) {
          $ref_ind = (int) $v[$this->fields["left"]];
          break;
          }
          }
          if ($node !== false && !$is_copy && $node[$this->fields["left"]] < $ref_ind) {
          $ref_ind -= $ndif;
          }

          $sql[] = "" .
          "UPDATE `" . $this->table . "` " .
          "SET `" . $this->fields["left"] . "` = `" . $this->fields["left"] . "` + " . $ndif . " " .
          "WHERE " .
          "`" . $this->fields["left"] . "` >= " . $ref_ind . " " .
          ( $is_copy ? "" : " AND `" . $this->fields["id"] . "` NOT IN (" . implode(",", $node_ids) . ") ");
          $sql[] = "" .
          "UPDATE `" . $this->table . "` " .
          "SET `" . $this->fields["right"] . "` = `" . $this->fields["right"] . "` + " . $ndif . " " .
          "WHERE " .
          "`" . $this->fields["right"] . "` >= " . $ref_ind . " " .
          ( $is_copy ? "" : " AND `" . $this->fields["id"] . "` NOT IN (" . implode(",", $node_ids) . ") ");

          $ldif = $ref_id == 0 ? 0 : $ref_node[$this->fields["level"]] + 1;
          $idif = $ref_ind;
          if ($node !== false) {
          $ldif = $node[$this->fields["level"]] - ($ref_node[$this->fields["level"]] + 1);
          $idif = $node[$this->fields["left"]] - $ref_ind;
          if ($is_copy) {
          $sql[] = "" .
          "INSERT INTO `" . $this->table . "` (" .
          "`" . $this->fields["parent_id"] . "`, " .
          "`" . $this->fields["position"] . "`, " .
          "`" . $this->fields["left"] . "`, " .
          "`" . $this->fields["right"] . "`, " .
          "`" . $this->fields["level"] . "`" .
          ") " .
          "SELECT " .
          "" . $ref_id . ", " .
          "`" . $this->fields["position"] . "`, " .
          "`" . $this->fields["left"] . "` - (" . ($idif + ($node[$this->fields["left"]] >= $ref_ind ? $ndif : 0)) . "), " .
          "`" . $this->fields["right"] . "` - (" . ($idif + ($node[$this->fields["left"]] >= $ref_ind ? $ndif : 0)) . "), " .
          "`" . $this->fields["level"] . "` - (" . $ldif . ") " .
          "FROM `" . $this->table . "` " .
          "WHERE " .
          "`" . $this->fields["id"] . "` IN (" . implode(",", $node_ids) . ") " .
          "ORDER BY `" . $this->fields["level"] . "` ASC";
          } else {
          $sql[] = "" .
          "UPDATE `" . $this->table . "` SET " .
          "`" . $this->fields["parent_id"] . "` = " . $ref_id . ", " .
          "`" . $this->fields["position"] . "` = " . $position . " " .
          "WHERE " .
          "`" . $this->fields["id"] . "` = " . $id;
          $sql[] = "" .
          "UPDATE `" . $this->table . "` SET " .
          "`" . $this->fields["left"] . "` = `" . $this->fields["left"] . "` - (" . $idif . "), " .
          "`" . $this->fields["right"] . "` = `" . $this->fields["right"] . "` - (" . $idif . "), " .
          "`" . $this->fields["level"] . "` = `" . $this->fields["level"] . "` - (" . $ldif . ") " .
          "WHERE " .
          "`" . $this->fields["id"] . "` IN (" . implode(",", $node_ids) . ") ";
          }
          } else {
          $sql[] = "" .
          "INSERT INTO `" . $this->table . "` (" .
          "`" . $this->fields["parent_id"] . "`, " .
          "`" . $this->fields["position"] . "`, " .
          "`" . $this->fields["left"] . "`, " .
          "`" . $this->fields["right"] . "`, " .
          "`" . $this->fields["level"] . "` " .
          ") " .
          "VALUES (" .
          $ref_id . ", " .
          $position . ", " .
          $idif . ", " .
          ($idif + 1) . ", " .
          $ldif .
          ")";
          }
          foreach ($sql as $q) {
          $this->db->query($q);
          }
          $ind = $this->db->insert_id();
          if ($is_copy)
          $this->_fix_copy($ind, $position);
          return $node === false || $is_copy ? $ind : true; */
    }

    function _fix_copy($id, $position) {/*
      $node = $this->_get_node($id);
      $children = $this->_get_children($id, true);

      $map = array();
      for ($i = $node[$this->fields["left"]] + 1; $i < $node[$this->fields["right"]]; $i++) {
      $map[$i] = $id;
      }
      foreach ($children as $cid => $child) {
      if ((int) $cid == (int) $id) {
      $this->db->query("UPDATE `" . $this->table . "` SET `" . $this->fields["position"] . "` = " . $position . " WHERE `" . $this->fields["id"] . "` = " . $cid);
      continue;
      }
      $this->db->query("UPDATE `" . $this->table . "` SET `" . $this->fields["parent_id"] . "` = " . $map[(int) $child[$this->fields["left"]]] . " WHERE `" . $this->fields["id"] . "` = " . $cid);
      for ($i = $child[$this->fields["left"]] + 1; $i < $child[$this->fields["right"]]; $i++) {
      $map[$i] = $cid;
      }
      } */
    }

    function _reconstruct() {
        /* $this->db->query("" .
          "CREATE TEMPORARY TABLE `temp_tree` (" .
          "`" . $this->fields["id"] . "` INTEGER NOT NULL, " .
          "`" . $this->fields["parent_id"] . "` INTEGER NOT NULL, " .
          "`" . $this->fields["position"] . "` INTEGER NOT NULL" .
          ") type=HEAP"
          );
          $this->db->query("" .
          "INSERT INTO `temp_tree` " .
          "SELECT " .
          "`" . $this->fields["id"] . "`, " .
          "`" . $this->fields["parent_id"] . "`, " .
          "`" . $this->fields["position"] . "` " .
          "FROM `" . $this->table . "`"
          );

          $this->db->query("" .
          "CREATE TEMPORARY TABLE `temp_stack` (" .
          "`" . $this->fields["id"] . "` INTEGER NOT NULL, " .
          "`" . $this->fields["left"] . "` INTEGER, " .
          "`" . $this->fields["right"] . "` INTEGER, " .
          "`" . $this->fields["level"] . "` INTEGER, " .
          "`stack_top` INTEGER NOT NULL, " .
          "`" . $this->fields["parent_id"] . "` INTEGER, " .
          "`" . $this->fields["position"] . "` INTEGER " .
          ") type=HEAP"
          );
          $counter = 2;
          $this->db->query("SELECT COUNT(*) FROM temp_tree");
          $this->db->nextr();
          $maxcounter = (int) $this->db->f(0) * 2;
          $currenttop = 1;
          $this->db->query("" .
          "INSERT INTO `temp_stack` " .
          "SELECT " .
          "`" . $this->fields["id"] . "`, " .
          "1, " .
          "NULL, " .
          "0, " .
          "1, " .
          "`" . $this->fields["parent_id"] . "`, " .
          "`" . $this->fields["position"] . "` " .
          "FROM `temp_tree` " .
          "WHERE `" . $this->fields["parent_id"] . "` = 0"
          );
          $this->db->query("DELETE FROM `temp_tree` WHERE `" . $this->fields["parent_id"] . "` = 0");

          while ($counter <= $maxcounter) {
          $this->db->query("" .
          "SELECT " .
          "`temp_tree`.`" . $this->fields["id"] . "` AS tempmin, " .
          "`temp_tree`.`" . $this->fields["parent_id"] . "` AS pid, " .
          "`temp_tree`.`" . $this->fields["position"] . "` AS lid " .
          "FROM `temp_stack`, `temp_tree` " .
          "WHERE " .
          "`temp_stack`.`" . $this->fields["id"] . "` = `temp_tree`.`" . $this->fields["parent_id"] . "` AND " .
          "`temp_stack`.`stack_top` = " . $currenttop . " " .
          "ORDER BY `temp_tree`.`" . $this->fields["position"] . "` ASC LIMIT 1"
          );

          if ($this->db->nextr()) {
          $tmp = $this->db->f("tempmin");

          $q = "INSERT INTO temp_stack (stack_top, `" . $this->fields["id"] . "`, `" . $this->fields["left"] . "`, `" . $this->fields["right"] . "`, `" . $this->fields["level"] . "`, `" . $this->fields["parent_id"] . "`, `" . $this->fields["position"] . "`) VALUES(" . ($currenttop + 1) . ", " . $tmp . ", " . $counter . ", NULL, " . $currenttop . ", " . $this->db->f("pid") . ", " . $this->db->f("lid") . ")";
          $this->db->query($q);
          $this->db->query("DELETE FROM `temp_tree` WHERE `" . $this->fields["id"] . "` = " . $tmp);
          $counter++;
          $currenttop++;
          } else {
          $this->db->query("" .
          "UPDATE temp_stack SET " .
          "`" . $this->fields["right"] . "` = " . $counter . ", " .
          "`stack_top` = -`stack_top` " .
          "WHERE `stack_top` = " . $currenttop
          );
          $counter++;
          $currenttop--;
          }
          }

          $temp_fields = $this->fields;
          unset($temp_fields["parent_id"]);
          unset($temp_fields["position"]);
          unset($temp_fields["left"]);
          unset($temp_fields["right"]);
          unset($temp_fields["level"]);
          if (count($temp_fields) > 1) {
          $this->db->query("" .
          "CREATE TEMPORARY TABLE `temp_tree2` " .
          "SELECT `" . implode("`, `", $temp_fields) . "` FROM `" . $this->table . "` "
          );
          }
          $this->db->query("TRUNCATE TABLE `" . $this->table . "`");
          $this->db->query("" .
          "INSERT INTO " . $this->table . " (" .
          "`" . $this->fields["id"] . "`, " .
          "`" . $this->fields["parent_id"] . "`, " .
          "`" . $this->fields["position"] . "`, " .
          "`" . $this->fields["left"] . "`, " .
          "`" . $this->fields["right"] . "`, " .
          "`" . $this->fields["level"] . "` " .
          ") " .
          "SELECT " .
          "`" . $this->fields["id"] . "`, " .
          "`" . $this->fields["parent_id"] . "`, " .
          "`" . $this->fields["position"] . "`, " .
          "`" . $this->fields["left"] . "`, " .
          "`" . $this->fields["right"] . "`, " .
          "`" . $this->fields["level"] . "` " .
          "FROM temp_stack " .
          "ORDER BY `" . $this->fields["id"] . "`"
          );
          if (count($temp_fields) > 1) {
          $sql = "" .
          "UPDATE `" . $this->table . "` v, `temp_tree2` SET v.`" . $this->fields["id"] . "` = v.`" . $this->fields["id"] . "` ";
          foreach ($temp_fields as $k => $v) {
          if ($k == "id")
          continue;
          $sql .= ", v.`" . $v . "` = `temp_tree2`.`" . $v . "` ";
          }
          $sql .= " WHERE v.`" . $this->fields["id"] . "` = `temp_tree2`.`" . $this->fields["id"] . "` ";
          $this->db->query($sql);
          } */
    }

    function _analyze() {/*
      $report = array();

      $this->db->query("" .
      "SELECT " .
      "`" . $this->fields["left"] . "` FROM `" . $this->table . "` s " .
      "WHERE " .
      "`" . $this->fields["parent_id"] . "` = 0 "
      );
      $this->db->nextr();
      if ($this->db->nf() == 0) {
      $report[] = "[FAIL]\tNo root node.";
      } else {
      $report[] = ($this->db->nf() > 1) ? "[FAIL]\tMore than one root node." : "[OK]\tJust one root node.";
      }
      $report[] = ($this->db->f(0) != 1) ? "[FAIL]\tRoot node's left index is not 1." : "[OK]\tRoot node's left index is 1.";

      $this->db->query("" .
      "SELECT " .
      "COUNT(*) FROM `" . $this->table . "` s " .
      "WHERE " .
      "`" . $this->fields["parent_id"] . "` != 0 AND " .
      "(SELECT COUNT(*) FROM `" . $this->table . "` WHERE `" . $this->fields["id"] . "` = s.`" . $this->fields["parent_id"] . "`) = 0 ");
      $this->db->nextr();
      $report[] = ($this->db->f(0) > 0) ? "[FAIL]\tMissing parents." : "[OK]\tNo missing parents.";

      $this->db->query("SELECT MAX(`" . $this->fields["right"] . "`) FROM `" . $this->table . "`");
      $this->db->nextr();
      $n = $this->db->f(0);
      $this->db->query("SELECT COUNT(*) FROM `" . $this->table . "`");
      $this->db->nextr();
      $c = $this->db->f(0);
      $report[] = ($n / 2 != $c) ? "[FAIL]\tRight index does not match node count." : "[OK]\tRight index matches count.";

      $this->db->query("" .
      "SELECT COUNT(`" . $this->fields["id"] . "`) FROM `" . $this->table . "` s " .
      "WHERE " .
      "(SELECT COUNT(*) FROM `" . $this->table . "` WHERE " .
      "`" . $this->fields["right"] . "` < s.`" . $this->fields["right"] . "` AND " .
      "`" . $this->fields["left"] . "` > s.`" . $this->fields["left"] . "` AND " .
      "`" . $this->fields["level"] . "` = s.`" . $this->fields["level"] . "` + 1" .
      ") != " .
      "(SELECT COUNT(*) FROM `" . $this->table . "` WHERE " .
      "`" . $this->fields["parent_id"] . "` = s.`" . $this->fields["id"] . "`" .
      ") "
      );
      $this->db->nextr();
      $report[] = ($this->db->f(0) > 0) ? "[FAIL]\tAdjacency and nested set do not match." : "[OK]\tNS and AJ match";

      return implode("<br />", $report); */
    }

    function _dump($output = false) {
        /*  $nodes = array();
          $this->db->query("SELECT * FROM " . $this->table . " ORDER BY `" . $this->fields["left"] . "`");
          while ($this->db->nextr())
          $nodes[] = $this->db->get_row("assoc");
          if ($output) {
          echo "<pre>";
          foreach ($nodes as $node) {
          echo str_repeat("&#160;", (int) $node[$this->fields["level"]] * 2);
          echo $node[$this->fields["id"]] . " (" . $node[$this->fields["left"]] . "," . $node[$this->fields["right"]] . "," . $node[$this->fields["level"]] . "," . $node[$this->fields["parent_id"]] . "," . $node[$this->fields["position"]] . ")<br />";
          }
          echo str_repeat("-", 40);
          echo "</pre>";
          }
          return $nodes; */
    }

    function _drop() {
        /* $this->db->query("TRUNCATE TABLE `" . $this->table . "`");
          $this->db->query("" .
          "INSERT INTO `" . $this->table . "` (" .
          "`" . $this->fields["id"] . "`, " .
          "`" . $this->fields["parent_id"] . "`, " .
          "`" . $this->fields["position"] . "`, " .
          "`" . $this->fields["left"] . "`, " .
          "`" . $this->fields["right"] . "`, " .
          "`" . $this->fields["level"] . "` " .
          ") " .
          "VALUES (" .
          "1, " .
          "0, " .
          "0, " .
          "1, " .
          "2, " .
          "0 " .
          ")"); */
    }

}

class json_tree extends _tree_struct {

    protected function ru2Lat($str) {

        $rus = array('ё', 'ж', 'ц', 'ч', 'ш', 'щ', 'ю', 'я', 'Ё', 'Ж', 'Ц', 'Ч', 'Ш', 'Щ', 'Ю', 'Я', 'Ї', 'ї', 'Є', 'є', 'І', 'і', 'ь', 'Ь', 'Ъ', 'ъ');
        $lat = array('yo', 'zh', 'tc', 'ch', 'sh', 'sh', 'yu', 'ya', 'YO', 'ZH', 'TC', 'CH', 'SH', 'SH', 'YU', 'YA', 'YI', 'yi', 'E', 'e', 'I', 'i', '', '', '', '');
        $prototype = array('q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm', 'Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P', 'A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L', 'Z', 'X', 'C', 'V', 'B', 'N', 'M', '-', '_', ' ', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', ':', '/', '.', '?', '&');

        /* if ($type == 'link') {
          array_push($prototype, ':', '/', '.', '?', '&');
          } */

        $str = str_replace($rus, $lat, $str);

        $str = strtr(iconv('utf-8', 'cp1251', $str), iconv('utf-8', 'cp1251', "АБВГДЕЗИЙКЛМНОПРСТУФХЪЫЬЭабвгдезийклмнопрстуфхыэ"), "ABVGDEZIJKLMNOPRSTUFH_I_Eabvgdezijklmnoprstufhie");

        $size = strlen($str);

        $temp = "";
        for ($i = 0; $i < $size; $i++) {
            if (in_array($str[$i], $prototype))
                $temp .= $str[$i];
        }

        $str = $temp;

        //$str = str_ireplace(' ', '-', $str);
        $str = preg_replace('/\W/', '-', trim($str));
        return (strtolower($str));
    }

    protected $defaultFieldsArray = array();

    function __construct($table = "tree", $fields = array(), $add_fields = array("title" => "title", "type" => "type")) {
        parent::__construct($table, $fields);
        if (is_file('config/configCatalog.php')) {
            require_once 'config/configCatalog.php';

            if (isset($defaultFields) && is_array($defaultFields) && count($defaultFields) > 0) {
                $this->defaultFieldsArray = $defaultFields;
            }
        }


        /*  $this->fields = array_merge($this->fields, $add_fields);
          $this->add_fields = $add_fields; */
    }

    protected function isFieldFormConfigFile($fieldTitle) {
        if (count($this->defaultFieldsArray) > 0) {
            foreach ($this->defaultFieldsArray as $key => $value) {
                $tmpVal = $value;
                if (is_array($value) && isset($value['title'])) {
                    $tmpVal = $value['title'];
                }

                if ($tmpVal == $fieldTitle) {
                    return $key;
                }
            }
        }
        return false;
    }

    function hide_node($data) {

        $id = false;
        $isNew = false;

        if (isset($data['id'])) {
            if (substr($data['id'], 0, 3) == 'cf_') {
                $isNew = false;
                $id = str_replace('cf_', '', $data['id']);
            } else {
                $id = $data['id'];
            }
        }

        if (is_numeric($id) && isset($data['type']) && $data['text'] && isset($data['artikul']) && isset($data['act'])) {

            $data['text'] = str_replace(' (Скрытое поле) ', '', $data['text']);

           

            $statusToGroup = 'field';

            if ($data['type'] == 'groups') {
                $statusToGroup = 'sub_group';
            }

            if ($data['type'] == 'base') {
                $statusToGroup = 'group';
            }

            if (($fieldName = $this->isFieldFormConfigFile($data['text']))) {

                 $layout = 'right';
                if (isset(CatalogDetailFieldsLayout::$bottom) && in_array($fieldName, CatalogDetailFieldsLayout::$bottom)) {
                    $layout = 'bottom';
                }    

                if (!($res = $this->db->fetchOne("SELECT `id` FROM `catalog_section_fields` WHERE `title` = '$data[text]' AND `is_default_field` = 'yes'"))) {                    

                    $this->db->insert('catalog_section_fields', array(
                        'name' => $fieldName,
                        'layout' => $layout,
                        'group_position' => '1',
                        'sub_group_position' => '1',
                        'title_position' => '1',
                        'title' => $data['text'],
                        'catalog_section_href' => $data['artikul'],
                        'group' => 'A1',
                        'sub_group' => 'A1',
                        'status' => $data['act'],
                        'is_default_field' => 'yes',
                        'status_to_group' => $statusToGroup
                    ));

                    if ($data['act'] == 'hidden') {
                        die('1');
                    } else {
                        die('0');
                    }
                } else {
                    $this->db->update('catalog_section_fields', array(
                        'name' => $fieldName,
                        'layout' => $layout,
                        'group_position' => '1',
                        'sub_group_position' => '1',
                        'title_position' => '1',
                        'title' => $data['text'],
                        'catalog_section_href' => $data['artikul'],
                        'group' => 'A1',
                        'sub_group' => 'A1',
                        'status' => $data['act'],
                        'status_to_group' => $statusToGroup,
                        'is_default_field' => 'yes',
                            ), "id='$id'");


                    $text = '1';
                    if ($data['act'] == 'hidden') {
                        die('1');
                    } else {
                        die('0');
                    }
                }
            } else {
                $this->db->update('catalog_section_fields', array('status' => $data['act'], 'status_to_group' => $statusToGroup), "id='$id'");
                if ($data['act'] == 'hidden') {
                        die('1');
                    } else {
                        die('0');
                    }
            }
        }
    }

    function create_node($data) {
        /*  $id = parent::_create((int) $data[$this->fields["id"]], (int) $data[$this->fields["position"]]);
          if ($id) {
          $data["id"] = $id;
          $this->set_data($data);
          return "{ \"status\" : 1, \"id\" : " . (int) $id . " }";
          }
          return "{ \"status\" : 0 }"; */
    }

    function set_data($data) {
        /* if (count($this->add_fields) == 0) {
          return "{ \"status\" : 1 }";
          }
          $s = "UPDATE `" . $this->table . "` SET `" . $this->fields["id"] . "` = `" . $this->fields["id"] . "` ";
          foreach ($this->add_fields as $k => $v) {
          if (isset($data[$k]))
          $s .= ", `" . $this->fields[$v] . "` = \"" . $this->db->escape($data[$k]) . "\" ";
          else
          $s .= ", `" . $this->fields[$v] . "` = `" . $this->fields[$v] . "` ";
          }
          $s .= "WHERE `" . $this->fields["id"] . "` = " . (int) $data["id"];
          $this->db->query($s);
          return "{ \"status\" : 1 }"; */
    }

    function rename_node($data) {
        //return $this->set_data($data);
    }

    function move_node($data) {
        $retArr = array();
        if (isset($data['artikul']) && !empty($data['artikul']) && isset($data['id']) && is_numeric($data['id']) && isset($data['position']) && is_numeric($data['position']) && (isset($data['type']) && (in_array($data['type'], array('group', 'sub_group', 'fields')) ))) {

            if ($data['type'] == 'group') {
                $groupsId = array();

                if (($gropName = $this->db->fetchOne("SELECT `group` FROM `catalog_section_fields` WHERE `id`='$data[id]'"))) {
                    $sql = "UPDATE `catalog_section_fields` SET `group_position` = $data[position] WHERE  `catalog_section_href`='$data[artikul]' AND `group` = '$gropName'";
                    $this->db->query($sql);
                    $sql = "UPDATE `catalog_section_fields` SET `group_position` = (`group_position`+1) WHERE  `catalog_section_href`='$data[artikul]' AND `group_position` >= $data[position]";
                    $this->db->query($sql);



                    if (($row = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href`='$data[artikul]' ORDER BY `group_position`"))) {
                        $index = 1;

                        foreach ($row as $res) {
                            $testData[] = $res;
                            if (!isset($groupsId[$res['group']])) {
                                $groupsId[$res['group']] = $res['id'];
                                $sql = "UPDATE `catalog_section_fields` SET `group_position` = $index WHERE  `catalog_section_href`='$data[artikul]' AND `group` = '$res[group]' ";
                                $this->db->query($sql);

                                $index++;
                            }
                        }

                        return "{ \"status\" : 1, \"id\" : " . $data['id'] . " }";
                    }
                }
            } elseif ($data['type'] == 'sub_group') {

                if (($selectedSubGrop = $this->db->fetchRow("SELECT * FROM `catalog_section_fields` WHERE `id`='$data[id]'"))) {
                    $sql = "UPDATE `catalog_section_fields` SET `sub_group_position` = $data[position] WHERE  `catalog_section_href`='$data[artikul]' AND `group`='$selectedSubGrop[group]' AND `sub_group` = '$selectedSubGrop[sub_group]'";
                    $this->db->query($sql);

                    $sql = "UPDATE `catalog_section_fields` SET `sub_group_position` = (`sub_group_position`+1) WHERE  `catalog_section_href`='$data[artikul]' AND `group`='$selectedSubGrop[group]' AND `sub_group_position` >= $data[position]";
                    $this->db->query($sql);


                    if (($row = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE  `catalog_section_href`='$data[artikul]' AND `group`='$selectedSubGrop[group]' ORDER BY `sub_group_position`"))) {
                        $index = 1;
                        $subGroupsArr = array();
                        foreach ($row as $res) {
                            if (!isset($subGroupsArr[$res['sub_group']])) {
                                $sql = "UPDATE `catalog_section_fields` SET `sub_group_position` = $index WHERE  `catalog_section_href`='$data[artikul]' AND `group` = '$res[group]' AND `sub_group`='$res[sub_group]'";
                                // print "$sql \n";
                                $this->db->query($sql);
                                $subGroupsArr[$res['sub_group']] = '';
                                $index++;
                            }
                        }
                        return "{ \"status\" : 1, \"id\" : " . $data['id'] . " }";
                    }
                }
            } elseif ($data['type'] == 'fields') {

                if (($selectedField = $this->db->fetchRow("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href` = '$data[artikul]' AND id='$data[id]'"))) {

                    $sql = "UPDATE `catalog_section_fields` SET `title_position` = (`title_position`+1) WHERE  `catalog_section_href`='$data[artikul]' AND `group`='$selectedField[group]' AND `sub_group`='$selectedField[sub_group]' AND `title` != '$selectedField[title]'";
                    $this->db->query($sql);

                    $layout = 'features_table';

                    if (isset($data['new_parent'])) {
                        if ($data['new_parent'] == 'bottom_group') {
                            $layout = 'bottom';
                        }

                        if ($data['new_parent'] == 'right_group') {
                            $layout = 'right';
                        }
                    }

                    $sql = "UPDATE `catalog_section_fields` SET `title_position` = $data[position], `layout`='$layout' WHERE  `catalog_section_href`='$data[artikul]' AND `group`='$selectedField[group]' AND `sub_group`='$selectedField[sub_group]' AND `title` = '$selectedField[title]'";
                    $this->db->query($sql);

                    if (($subGroups = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href` = '$data[artikul]' AND `sub_group`= '$selectedField[sub_group]'  AND `group` = '$selectedField[group]' ORDER BY  `title_position` "))) {
                        $subGroupLength = 0;
                        $uniqSubGroupsName = array();
                        $index = 1;
                        foreach ($subGroups as $subGroup) {
                            $subGroupName = $subGroup['title'];

                            if (!isset($uniqSubGroupsName[$subGroup['group']]) || !in_array($subGroup['title'], $uniqSubGroupsName[$subGroup['group']])) {
                                $uniqSubGroupsName[$subGroup['group']][] = $subGroup['title'];
                                $result[] = array(
                                    "attr" => array("id" => "node_" . $subGroup['id'], "rel" => 'fields'),
                                    "data" => $subGroupName,
                                    "state" => ""
                                );

                                $sql = "UPDATE `catalog_section_fields` SET `title_position` = $index WHERE  `catalog_section_href`='$data[artikul]' AND `group`='$subGroup[group]' AND `sub_group`='$subGroup[sub_group]' AND `title`='$subGroup[title]'";
                                $this->db->query($sql);
                                $index++;
                                $subGroupLength++;
                            }
                        }



                        return "{ \"status\" : 1, \"id\" : " . $data['id'] . " }";
                    }
                }
            }
        }
        return "{ \"status\" : 0, \"id\" : " . $data['id'] . " }";
    }

    function remove_node($data) {
        /* $id = parent::_remove((int) $data["id"]);
          return "{ \"status\" : 1 }"; */
    }

    function get_children($data) {

        $uniqGroupsName = array();
        $result = array();

        if ($data['id'] == '-1' && $data['rel'] == 'group') {
            $result[] = array(
                "attr" => array("id" => "node_0", "rel" => 'right_group'),
                "data" => 'Поля справа от основного фото товара',
                "state" => /* ((int) $v[$this->fields["right"]] - (int) $v[$this->fields["left"]] > 1) ? */"closed" //: ""
            );
            $result[] = array(
                "attr" => array("id" => "node_0", "rel" => 'bottom_group'),
                "data" => 'Поля под описанием товара',
                "state" => /* ((int) $v[$this->fields["right"]] - (int) $v[$this->fields["left"]] > 1) ? */"closed" //: ""
            );

            $result[] = array(
                "attr" => array("id" => "node_0", "rel" => 'base'),
                "data" => 'Характеристики товара',
                "state" => /* ((int) $v[$this->fields["right"]] - (int) $v[$this->fields["left"]] > 1) ? */"closed" //: ""
            );
        }

        $retTst = array();

        if (isset($data['id']) && is_numeric($data['id']) && isset($data['rel']) && !empty($data['rel'])) {



            if ($data['id'] == '0' && $data['rel'] == 'right_group') {
                $groupsTmp = array();
                
                $groups = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href` = '$data[artikul]' AND `layout` = 'right' ORDER BY `group_position`  ASC");
                if (!$groups) {
                    $groups = array();
                } else {
                    foreach ($groups as $key => $val) {
                        $groupsTmp[$val['name']] = $val;
                    }
                }
                if (isset(CatalogDetailFieldsLayout::$right)) {                                        
                    foreach (CatalogDetailFieldsLayout::$right as $key => $val) {
                        
                       if (!isset($groupsTmp[$val])) {
                        $groupsTmp[$val] = array('id' => 'cf_' . $key,
                            'name' => $val,
                            'layout' => 'right',
                            'group' => 'A1',
                            'group_position' => $key,
                            'sub_group' => 'A1',
                            'sub_group_position' => $key,
                            'title_position' => $key,
                            'title' => '[__TITLE__]',
                            'type' => 'varchar',
                            'status' => 'show',
                            'language' => 'ru');                    
                    }
                    }
                    
                }
                
                $groups = $groupsTmp;
              

                if ($groups) {

                    $index = 1;

                    foreach ($groups as $key => $group) {
                     
                        //$ln = $this->db->update('catalog_section_fields', array('group_position' => $index), "id='$group[id]'");
                        $sql = "UPDATE `catalog_section_fields` SET `title_position` = $index WHERE `layout` = 'right_group'";
                        $this->db->query($sql);
                        $retTst[] = $group;

                        if ($group['title'] == '[__TITLE__]') {

                            $tmpTitle = '';
                            if (isset($this->defaultFieldsArray[$group['name']])) {
                                $tmpTitle = $this->defaultFieldsArray[$group['name']];
                            }

                            if (is_array($this->defaultFieldsArray[$group['name']]) && isset($this->defaultFieldsArray[$fieldValue['name']]['title'])) {
                                $tmpTitle = $this->defaultFieldsArray[$group['name']]['title'];
                            }

                            $group['title'] = str_replace('[__TITLE__]', $tmpTitle, $group['title']);

                            if (isset($this->activePage[$group['name']])) {
                                $val = $this->activePage[$group['name']];
                            }
                        }
                        $index++;
                        if ($group['status'] == 'hidden') {
                            $group['title'] .= ' (Скрытое поле) ';
                        }
                        $result[] = array(
                            "attr" => array("id" => "node_" . $group['id'], "rel" => 'fields'),
                            "data" => $group['title'],
                            "state" => /* ((int) $v[$this->fields["right"]] - (int) $v[$this->fields["left"]] > 1) ? */"closed", //: ""
                        );
                    }
                   
                }
                
                
                
            } elseif ($data['id'] == '0' && $data['rel'] == 'bottom_group') {

                $groups = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href` = '$data[artikul]' AND `layout` = 'bottom' ORDER BY `group_position`  ASC");

               $groupsTmp = array();               
            
                if (!$groups) {
                    $groups = array();
                } else {
                    foreach ($groups as $key => $val) {
                        $groupsTmp[$val['name']] = $val;
                    }
                }
                if (isset(CatalogDetailFieldsLayout::$bottom)) {                                        
                    foreach (CatalogDetailFieldsLayout::$bottom as $key => $val) {
                        
                       if (!isset($groupsTmp[$val])) {
                        $groupsTmp[$val] = array('id' => 'cf_' . $key,
                            'name' => $val,
                            'layout' => 'bottom',
                            'group' => 'A1',
                            'group_position' => $key,
                            'sub_group' => 'A1',
                            'sub_group_position' => $key,
                            'title_position' => $key,
                            'title' => '[__TITLE__]',
                            'type' => 'varchar',
                            'status' => 'show',
                            'language' => 'ru');                    
                    }
                    }
                    
                }
                
                $groups = $groupsTmp;
                if ($groups) {

                    $index = 1;

                    foreach ($groups as $key => $group) {

                        //$ln = $this->db->update('catalog_section_fields', array('group_position' => $index), "id='$group[id]'");
                        $sql = "UPDATE `catalog_section_fields` SET `title_position` = $index WHERE `layout` = 'bottom'";
                        $this->db->query($sql);
                        $retTst[] = $group;

                        if ($group['title'] == '[__TITLE__]') {

                            $tmpTitle = '';
                            if (isset($this->defaultFieldsArray[$group['name']])) {
                                $tmpTitle = $this->defaultFieldsArray[$group['name']];
                            }

                            if (is_array($this->defaultFieldsArray[$group['name']]) && isset($this->defaultFieldsArray[$fieldValue['name']]['title'])) {
                                $tmpTitle = $this->defaultFieldsArray[$group['name']]['title'];
                            }

                            $group['title'] = str_replace('[__TITLE__]', $tmpTitle, $group['title']);

                            if (isset($this->activePage[$group['name']])) {
                                $val = $this->activePage[$group['name']];
                            }
                        }


                        $index++;
                        if ($group['status'] == 'hidden') {
                            $group['title'] .= ' (Скрытое поле) ';
                        }
                        $result[] = array(
                            "attr" => array("id" => "node_" . $group['id'], "rel" => 'fields'),
                            "data" => $group['title'],
                            "state" => /* ((int) $v[$this->fields["right"]] - (int) $v[$this->fields["left"]] > 1) ? */"closed", //: ""
                        );
                    }


                    // print_r($retTst); die;
                }
            } elseif ($data['id'] == '0' && $data['rel'] == 'base') {
                if (($groups = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href` = '$data[artikul]' AND `layout` = 'features_table' ORDER BY `group_position`  ASC"))) {

                    $index = 1;

                    foreach ($groups as $group) {
                        if (!in_array($group['group'], $uniqGroupsName)) {
                            $uniqGroupsName[] = $group['group'];
                            $subGroupsStr = '';
                            $groupName = $group['group'];

                            if ($groupName == 'A1') {
                                $groupName = 'Основная группа';
                            }

                            if (empty($groupName)) {
                                $groupName = 'Незвание гурппы не указано';
                            }

                            //$ln = $this->db->update('catalog_section_fields', array('group_position' => $index), "id='$group[id]'");
                            $sql = "UPDATE `catalog_section_fields` SET `group_position` = $index WHERE `group` = '$group[group]'";
                            $this->db->query($sql);
                            $retTst[] = $group;


                            $index++;
                            if ($group['status'] == 'hidden') {
                                $group['title'] .= ' (Скрытое поле) ';
                            }
                            $result[] = array(
                                "attr" => array("id" => "node_" . $group['id'], "rel" => 'group'),
                                "data" => $groupName,
                                "state" => /* ((int) $v[$this->fields["right"]] - (int) $v[$this->fields["left"]] > 1) ? */"closed", //: ""
                            );
                        }
                    }


                    // print_r($retTst); die;
                }
            } elseif ($data['rel'] == 'group') {

                if (($subGroups = $this->db->fetchAll("SELECT `c2`.* FROM `catalog_section_fields` as `c1`, `catalog_section_fields` as `c2` WHERE `c2`.`catalog_section_href` = '$data[artikul]' AND `c2`.`group`= `c1`.`group` AND `c1`.id='$data[id]'  AND `c2`.`layout` = 'features_table'  ORDER BY `sub_group_position` "))) {
                    $subGroupLength = 0;
                    $uniqSubGroupsName = array();
                    $index = 1;
                    foreach ($subGroups as $subGroup) {
                        $subGroupName = $subGroup['sub_group'];
                        if ($subGroupName == 'A1') {
                            $subGroupName = 'Дополнительные параметры';
                        }
                        if (!isset($uniqSubGroupsName[$subGroup['group']]) || !in_array($subGroup['sub_group'], $uniqSubGroupsName[$subGroup['group']])) {
                            $uniqSubGroupsName[$subGroup['group']][] = $subGroup['sub_group'];
                            $result[] = array(
                                "attr" => array("id" => "node_" . $subGroup['id'], "rel" => 'sub_group'),
                                "data" => $subGroupName,
                                "state" => /* ((int) $v[$this->fields["right"]] - (int) $v[$this->fields["left"]] > 1) ? */"closed" //: ""
                            );

                            $sql = "UPDATE `catalog_section_fields` SET `sub_group_position`=$index WHERE `group`='$subGroup[group]' AND `sub_group` = '$subGroup[sub_group]'";
                            //   print $sql;
                            $this->db->query($sql);
                            $index++;

                            $subGroupLength++;
                        }
                    }



                    if ($subGroupLength == 0) {
                        
                    }
                }
            } elseif ($data['rel'] == 'sub_group') {

                if (($subGroupsTmp = $this->db->fetchRow("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href` = '$data[artikul]' AND id='$data[id]'  AND `layout` = 'features_table' "))) {

                    if (($subGroups = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href` = '$data[artikul]'  AND `layout` = 'features_table' AND `sub_group`= '$subGroupsTmp[sub_group]'  AND `group` = '$subGroupsTmp[group]' ORDER BY `title_position` "))) {
                        $subGroupLength = 0;
                        $uniqSubGroupsName = array();
                        $index = 1;
                        foreach ($subGroups as $subGroup) {
                            $subGroupName = $subGroup['title'];

                            //if (!isset($uniqSubGroupsName[$subGroup['group']]) || !in_array($subGroup['title'], $uniqSubGroupsName[$subGroup['group']])) {                     
                            $uniqSubGroupsName[$subGroup['group']][] = $subGroup['title'];
                            if ($subGroup['status'] == 'hidden') {
                                $subGroupName .= ' (Скрытое поле) ';
                            }
                            $result[] = array(
                                "attr" => array("id" => "node_" . $subGroup['id'], "rel" => 'fields'),
                                "data" => $subGroupName,
                                "state" => ""
                            );

                            $sql = "UPDATE `catalog_section_fields` SET `title_position` = $index WHERE `sub_group`= '$subGroup[sub_group]'  AND `group` = '$subGroup[group]' AND `title`='$subGroup[title]'";

                            $this->db->query($sql);
                            $index++;


                            $subGroupLength++;
                            // }
                        }

                        if ($subGroupLength == 0) {
                            
                        }
                    }
                }
            }
        }

        //  $result['dump'] = print_r($retTst, true);

        return json_encode($result);
    }

    function search($data) {

        if (isset($data['search_str']) && !empty($data['search_str'])) {
            if (($row = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE `title` LIKE '%$data[search_str]%'"))) {
                /*
                 * [{
                 *    "attr": {
                 *       "id":"node_390",
                 *       "rel":"default"
                 * },
                 * "data":"New node",
                 * "state":""
                 * },
                 * {"attr":{
                 *    "id":"node_395",
                 *    "rel":"default"
                 * },
                 *    "data":"New node",
                 *    "state":""
                 *    }
                 * ]
                 */
                $ret = '[';
                $index = 0;

                foreach ($row as $res) {
                    if ($index > 0) {
                        $ret .= ',';
                    }
                    $index++;
                    $ret .= "{\"attr\":{\"id\":\"node_$res[id]\", \"rel\":\"fields\"}, \"data\":\"$res[title]\", \"state\":\"opened\"}";
                }
                $ret .= ']';
                return $ret;
            }
        }
        return "[]";
    }

    function _create_default() {
        /* $this->_drop();
          $this->create_node(array(
          "id" => 1,
          "position" => 0,
          "title" => "C:",
          "type" => "drive"
          ));
          $this->create_node(array(
          "id" => 1,
          "position" => 1,
          "title" => "D:",
          "type" => "drive"
          ));
          $this->create_node(array(
          "id" => 2,
          "position" => 0,
          "title" => "_demo",
          "type" => "folder"
          ));
          $this->create_node(array(
          "id" => 2,
          "position" => 1,
          "title" => "_docs",
          "type" => "folder"
          ));
          $this->create_node(array(
          "id" => 4,
          "position" => 0,
          "title" => "index.html",
          "type" => "default"
          ));
          $this->create_node(array(
          "id" => 5,
          "position" => 1,
          "title" => "doc.html",
          "type" => "default"
          )); */
    }

}

$jstree = new json_tree();
$jstree->setDb($db);

//$jstree->_create_default();
//die();

if (isset($_GET["reconstruct"])) {
    $jstree->_reconstruct();
    die();
}
if (isset($_GET["analyze"])) {
    echo $jstree->_analyze();
    die();
}

if ($_REQUEST["operation"] && strpos($_REQUEST["operation"], "_") !== 0 && method_exists($jstree, $_REQUEST["operation"])) {
    header("HTTP/1.0 200 OK");
    header('Content-type: application/json; charset=utf-8');
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Pragma: no-cache");
    echo $jstree->{$_REQUEST["operation"]}($_REQUEST);
    die();
}
header("HTTP/1.0 404 Not Found");
?>
