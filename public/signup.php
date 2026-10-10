<!-- Modal -->
<div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="signupModalLabel">Signup</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="partitions/signUp_handler.php" method="post">
                    <div class="form-group">
                        <label for="signEmail">Username</label>
                        <input type="text" class="form-control" id="signEmail" name="signEmail" aria-describedby="emailHelp">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword1">Confirm Password</label>
                        <input type="password" class="form-control" id="Cpassword" name="Cpassword">
                    </div>

                    <button type="submit" class="btn btn-primary">Signup</button>
                </form>
            </div>

        </div>
    </div>
</div>