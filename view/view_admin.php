<!DOCTYPE html>
<html lang="en">
<head>
  <base href="<?= $web_root ?>" />
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Admin</title>
  <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
  <script>
    async function addTricount() {
      const selectedOption = $('#notSubscribedTricounts option:selected');
      const selectedUser = $('#comboAllUsers option:selected');
      const userId = <?= $selectedUserId ?>;
      const userName = selectedUser.text();
      const tricountId = selectedOption.val();
      const tricountTitle = selectedOption.text();

      // Ajouter l'option sélectionnée à la comboBox "subscribedTricounts"
      $('#subscribedTricounts').append(selectedOption);
      await $.post("participation/add_service/" + tricountId, { "names": userId });

      //alert("Selected USER ID: " + userId);
      //alert("Selected USER Name " + userName);
      //alert("Selected Tricount ID: " + tricountId);
      //alert("Selected Tricount Title: " + tricountTitle);

    }

    async function delTricount() {
      const selectedOption = $('#subscribedTricounts option:selected');
      const selectedUser = $('#comboAllUsers option:selected');
      const userId = <?= $selectedUserId ?>;
      const userName = selectedUser.text();
      const tricountId = selectedOption.val();
      const tricountTitle = selectedOption.text();

      $('#notSubscribedTricounts').append(selectedOption);

      await $.post("participation/delete_service/" + tricountId, { "userId": userId });
    }

  </script>
</head>
  <body>
      <div>
          <?= $user->getFullName() ?> ---  Admin View!
      </div>
      <div id="comboAllUsers">
        <div class="edit-selectSub">
            <form action="tricount/admin_view" method="post">
                <select class="selectSub" name="userId">
                    <option value="">--All Users --</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u->getUserId() ?>" <?= ($u->getUserId() == $selectedUserId) ? 'selected' : '' ?>><?= $u->getFullName() ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" id="btnAddSubscriber">SHOW</button>
            </form>
        </div>
      </div>

      <div id="comboParticipate" >
              <div  class="edit-selectSub">
                  <select class="selectSub" name="names" id="subscribedTricounts">
                      <option value="">-- Participate --</option>
                        <?php foreach ($subscriberTricount as $ST): ?>
                          <option id="subValue" value='<?= $ST->get_id() ?>'><?= $ST->get_title() ?></option>
                        <?php endforeach; ?>
                  </select>
                  <button id="btnAddSubscriber" onclick='delTricount()'>Remove from Participate</button>
              </div>
      </div>

      <div id="comboNotParticipate" >
          <div  class="edit-selectSub">
                    <select class="selectSub" name="names" id="notSubscribedTricounts">
                        <option value="">-- NOT Participate --</option>
                          <?php foreach ($noSubscribeTricount  as $NST): ?>
                            <option id="subValue" data-tricount-id="<?= $NST->get_id() ?>" value='<?= $NST->get_id() ?>'><?= $NST->get_title() ?></option>
                          <?php endforeach; ?>
                    </select>
                    <button id="btnAddSubscriber" onclick='addTricount()'>Add to Participate</button>
                </div>
      </div>
  </body>
</html>
