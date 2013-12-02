<?php
    require_once 'database.php';

#Get lists that users created. Returns default list for display.
function getLists($user) {
  try {
    require 'config.php';
    $db = new KissDatabase($config);
  }
  catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }

  $first = false;
  $firstList = 0;

  try {
    foreach($db->getListsFromUser($user) as $row)
    {
      echo "<li><a href='lists.php?listid=" . $row['listid'] 
            . "'>" . $db->getListName($row['listid']) . "</a></li>";

      if ($first == false) {
      $first = true;
      $firstList = $row['listid'];
      }
    }
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
  return $firstList;
}

function editList($listid) {
  echo "<form class=\"form-inline\" role=\"form\" action=\"addForms.php\" method=\"post\">
          <div class=\"form-group\">
            <label class=\"sr-only\" for=\"itemName\"></label>
              <input type=\"text\" class=\"form-control input-normal\" id=\"itemName\" name=\"itemName\" placeholder=\"Item Name\">
          </div>
          <br />
          <div class=\"form-group\">
            <div class=\"span2\">
              <label class=\"sr-only\" for=\"quantity\"></label>
              <input type=\"number\" class=\"form-control input-small\" id=\"quantity\" name=\"quantity\" placeholder=\"Quantity\" min=\"0\">

              <label class=\"sr-only\" for=\"unit\"></label>
              <input type=\"text\" class=\"form-control input-small\" id=\"unit\" name=\"unit\" placeholder=\"Unit\">

            </div>

            <label class=\"sr-only\" for=\"listid\"></label>
            <input type=\"hidden\" class=\"form-control input-small\" id=\"listid\" name=\"listid\" value=\"$listid\">
          </div>
          <br />
          <div class=\"buttonPos-item\">
            <button type=\"submit\" class=\"btn btn-default\" name=\"addItem\">Add Item</button>
          </div>
        </form>";
}

//Add users to access list
function addEditors($listid) {
  echo "<form class=\"form-inline\" role=\"form\" action=\"addForms.php\" method=\"post\">
          <div class=\"form-group\">
            <label class=\"sr-only\" for=\"username\"></label>
            <input type=\"text\" class=\"form-control input-normal\" id=\"username\" name=\"username\" placeholder=\"Username\">

            <label class=\"sr-only\" for=\"listid\"></label>
            <input type=\"hidden\" class=\"form-control input-small\" id=\"listid\" name=\"listid\" value=\"$listid\">
            <button type=\"submit\" class=\"btn btn-default\" name=\"addUser\">Add User</button>
          </div>
        </form>";
}

function addNewList() {
  echo "<form class=\"form-inline\" role=\"form\" action=\"addForms.php\" method=\"post\">
          <div class=\"form-group\">
            <label class=\"sr-only\" for=\"listName\"></label>
              <input type=\"text\" class=\"form-control list-input\" id=\"listName\" name=\"listName\" placeholder=\"List Name\">
          </div>
          <button type=\"submit\" class=\"btn btn-default\" name=\"addList\">Add List</button>
        </form>";
}
?>