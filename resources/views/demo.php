<!DOCTYPE html>
<html>
<meta charset="utf-8">
<head>
    <title>Attribution Demo</title>
    <meta property="og:title" content="Attribution Demo"/>
    <meta property="og:url" content="http://attribution-tracking.appspot.com/"/>
    <link rel="canonical" href="http://attribution-tracking.appspot.com/" />

</head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<body>

<br>

<h2>Submit your name to get a personalized link.
    <form method="get" action="/" id="personalizer">
        <input type="username" id="username" value="" placeholder="Username" required/ >
        <input type=submit>
    </form>


    <br>
    <script>
        function fbShare(url, winWidth, winHeight) {
            var winTop = (screen.height / 2) - (winHeight / 2);
            var winLeft = (screen.width / 2) - (winWidth / 2);
            window.open('http://www.facebook.com/sharer.php?u=' + url + "?share=1", 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);
        }
        $(function(){
            $("#share").click(function(){
                fbShare('http://attribution-tracking.appspot.com/', 520, 350);
                $.post("/share", {username: location.pathname.split("/").pop(), url: location.href});
            });

            $("#personalizer").submit(function(){
                var username = $("#username").val();
                $.post("/create", { "username": username, "recruited_by" : location.pathname.split("/").pop() }, function(d){
                    window.location = "/share/" + username;
                });
                return false;
            })
        })
    </script>
</body>
</html>