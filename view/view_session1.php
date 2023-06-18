<!DOCTYPE html>
<html lang="en">

<head>
    <title>Session1</title>
    <base href="<?= $web_root ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="lib/jquery-3.6.3.min.js"></script>
    <script>
      async function add(){
        console.log("ADD");
        let notSubscribedTricount = $('#notSubscribedTricount option:selected');
        let notSubscribedTricountId = notSubscribedTricount.val();
        let id = "<?= $selectedUser ?>";
        await $.post("participation/add_service/" + notSubscribedTricountId, {names:id} );
        $('#subscribedTricount').append(notSubscribedTricount);
      }

      async function remove(){
        console.log("REMOVE");
        let subscribedTricount = $('#subscribedTricount option:selected');
        let subscribedTricountId = subscribedTricount.val();
        let id = "<?= $selectedUser ?>";
        await  $.post("participation/delete_service/" + subscribedTricountId, {userId:id});
        $('#notSubscribedTricount').append(subscribedTricount);
      }

    </script>

</head>

<body>
    <nav class="navbar  fixed-top  navbar-expand-lg" style="background-color: #e3f2fd;">
        <div class="container-fluid">
            <a class="btn btn-sm btn-outline-danger" type="button" href="user">Back</a>
            <span class="navbar-text"><b>Session 1</b></span>
        </div>
    </nav>
    <div class="pt-5 pb-3"></div>
    <div class="main pb-2">
        <form action="tricount/session1" method="post">
            <div class="row">
                <div class="col-9">
                    <select name="userId">
                        <option>-- Select a User --</option>
                        <?php foreach ($users as $u): ?>
                            <option id="subValue" value='<?= $u->getUserId() ?>'><?= $u->getFullName() ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-3">
                    <button class="btn btn-outline-secondary" type="submit">Show</button>
                </div>
            </div>
        </form>

            <div class="form-label mt-2">Participates in these tricounts</div>
            <select size=5 class="form-select" id="subscribedTricount">
              <?php foreach ($subscribedTricount as $ST): ?>
                <option id="subValue" value='<?= $ST->get_id() ?>'><?= $ST->get_title() ?></option>
              <?php endforeach; ?>
            </select>
            <div class="button-container">
                <button type="button" onclick="add()">
                  up
                </button>
                <button type="button" onclick="remove()">
                  remove
                </button>
            </div>

            <div class="form-label mt-2">Does not participate in these tricounts</div>
            <select size=5 class="form-select" id="notSubscribedTricount">
            <?php foreach ($notSubscribedTricount as $NST): ?>
                <option id="subValue" value='<?= $NST->get_id() ?>'><?= $NST->get_title() ?></option>
              <?php endforeach; ?>
            </select>

    </div>

</body>

</html>
