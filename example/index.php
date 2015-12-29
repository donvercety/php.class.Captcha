<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Captcha.php</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="col-md-12 text-center">
                    <h3>Captcha:</h3>
                </div>
                <div class="col-md-12 text-center">
                    <img src="./main.php" class="img-thumbnail" alt="Captcha Image" id="img" />
                	<br />
                	<br />
                </div>
                
               

				<div class="col-md-8 col-md-offset-2 text-center">
		            <form action="">
		            	<p class="help-block text-center" id="error-text">&nbsp;</p>
		                <div class="input-group">
		                    <input type="text" class="form-control" id="check" name="check" placeholder="captcha" />
		                    <span class="input-group-btn">
		                        <input type="submit" class="btn btn-success" id="refresh" value="Refresh" />
		                    </span>
		                </div>
		                <br />
						<button type="submit" class="btn btn-info btn-block" id="submit">Check</button>
		            </form>
				</div>
            </div>
        </div>
    </div>

    <script type="application/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script type="application/javascript">

        var check = $("#check"),
            errText = $("#error-text");

        $("#refresh").on("click", function(ev) {
            ev.preventDefault();
            $("#img").attr("src","./main.php?refresh" + (+new Date().getTime()));
            check.val("");
            errText.html("&nbsp;");
        });

        $("#submit").on("click", function(ev) {
            ev.preventDefault();

            var code = $("#check").val(),
                url  = "http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>/main.php?check="

            $.get(url + code, function(data) {
                if (data.isok) {
                    errText.html("<span class=\"text-success\">Valid</span>");
                } else {
                    errText.html("<span class=\"text-danger\">Invalid</span>");
                }
            });
        });
    </script>
</body>
</html>
