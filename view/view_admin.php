<!DOCTYPE html>
<html lang="en">
<head>
<base href="<?= $web_root ?>" />
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin's View</title>
  <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
  <script>
    let operationsList;
    let operations;
    let userId = "<?= $selectedUserId?>";

    $(function(){
      operationsList = $('#operationsList');

    });

    async function addTricount() {
      const selectedOption = $('#notSubscribedTricount option:selected');
      const selectedUser = $('#comboAllUsers option:selected');
      const userId = "<?= $selectedUserId?>";
      const userName = selectedUser.text();
      const tricountId = selectedOption.val();
      const tricountTitle = selectedOption.text();


      // Ajouter l'option sélectionnée à la comboBox "subscribedTricounts"
      $('#subscribedTricount').append(selectedOption);
      await $.post("participation/add_service/" + tricountId, { "names": userId });

    }

    async function delTricount() {
      console.log("DELETE");
      const selectedOption = $('#subscribedTricount option:selected');
      const selectedUser = $('#selectedUserId option:selected');
      const userId = "<?= $selectedUserId ?>";
      const userName = selectedUser.text();
      const tricountId = selectedOption.val();
      const tricountTitle = selectedOption.text();


      $('#notSubscribedTricount').append(selectedOption);

      await $.post("participation/delete_service/" + tricountId, { "userId": userId });
    }

    async function getOpeByTricountAndInitiator() {
      console.log("OPERATION")
      const tricount = $('#subscribedTricount option:selected');
      const tricountId = tricount.val();
      console.log(tricountId);
      console.log(userId);
      try {
            operations = await $.getJSON("tricount/get_operation_service/" + tricountId +"/"+ userId);
            displayOperation();
          } catch(e) {
            console.log(e);
            operationsList.html("<tr><td>Error encountered while retrieving the messages!</td></tr>");
          }
    }

    function displayOperation(){
      let html = "";
      for (let o of operations) {

                    html += "<tr>";
                    html += "<td>" + o.title + "</td>";
                    html += "<td>" + o.amount+ "</td>";
                    //html += "<td><input type='checkbox' disabled" + (m.private ? ' checked' : '') + "></td>";
                    html += "</tr>";
                }
                operationsList.html(html);
    }
  </script>
</head>
<body>
  <div class="title">
    <?= $user->getFullName() ?> -- Admin's View!
  </div>

  <div id="AllUsersCombo">
    <form action="tricount/admin/" method="post">
      <div class="edit-selectSub">
        <select class="selectSub" name="selectedUserId" id="selectedUserId">
          <option value="">--All Users--</option>
            <?php foreach ($users as $u): ?>
              <option id="subValue" value='<?= $u->getUserId() ?>'><?= $u->getFullName() ?></option>
            <?php endforeach; ?>
        </select>
        <button id="showSubscriber">SHOW</button>
      </div>
    </form>
  </div>

  <div id="subscribedTricountCombo">

      <div class="edit-selectSub">
        <select class="selectSub" name="subscribedTricount" id="subscribedTricount" onchange="getOpeByTricountAndInitiator()">
          <option value="" >--All Subscribed Tricount--</option>
            <?php foreach ($subscribedTricount as $ST): ?>
              <option id="subValue" value='<?= $ST->get_id() ?>' ><?= $ST->get_title() ?></option>
            <?php endforeach; ?>
        </select>
        <button id="showSubscriber" onclick="delTricount()" >Remove</button>
      </div>

  </div>

  <div id="notSubscribedTricountCombo">
    <form action="tricount/admin/" method="post">
      <div class="edit-selectSub">
        <select class="selectSub" name="notSubscribedTricount" id="notSubscribedTricount">
          <option value="">--Not Subscribed Tricount--</option>
            <?php foreach ($notSubscribedTricount as $NST): ?>
              <option id="subValue" value='<?= $NST->get_id() ?>'><?= $NST->get_title()  ?></option>
            <?php endforeach; ?>
        </select>
        <button id="showSubscriber" onclick="addTricount()">Add</button>
      </div>
    </form>
  </div>

  <div id="operationsList">

  </div>


</body>
</html>
