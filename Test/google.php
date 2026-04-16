<!DOCTYPE html>
<html>
<head>
    <title>Google Login Test</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial;
            background: #f5f5f5;
        }

        .box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Login</h2>

    <div id="g_id_onload"
         data-client_id="183897060025-l86sijis09ea760o8pcs3pvluj5o84ti.apps.googleusercontent.com"
         data-callback="handleCredentialResponse">
    </div>

    <div class="g_id_signin" data-type="standard"></div>

    <p id="userData"></p>
</div>

<script src="https://accounts.google.com/gsi/client" async defer></script>

<script>
function parseJwt(token) {
    let base64Url = token.split('.')[1];
    let base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
    let jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
    }).join(''));

    return JSON.parse(jsonPayload);
}

function handleCredentialResponse(response) {
    const data = parseJwt(response.credential);

    console.log(data);

    document.getElementById("userData").innerHTML =
        "Welcome " + data.name + "<br>Email: " + data.email;
}
</script>

</body>
</html>