<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Payment Form</title>
        <link rel="stylesheet" href="https://codepen.io/gymratpacks/pen/VKzBEp#0">
        <link href='https://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/main.css">
        <style type="text/css">
          *, *:before, *:after {
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}

body {
  font-family: 'Nunito', sans-serif;
  color: #384047;
}

form {
  max-width: 300px;
  margin: 10px auto;
  padding: 10px 20px;
  background: #f4f7f8;
  border-radius: 8px;
}

h1 {
  margin: 0 0 30px 0;
  text-align: center;
}

input[type="text"],
input[type="password"],
input[type="date"],
input[type="datetime"],
input[type="email"],
input[type="number"],
input[type="search"],
input[type="tel"],
input[type="time"],
input[type="url"],
textarea,
select {
  background: rgba(255,255,255,0.1);
  border: none;
  font-size: 16px;
  height: auto;
  margin: 0;
  outline: 0;
  padding: 15px;
  width: 100%;
  background-color: #e8eeef;
  color: #8a97a0;
  box-shadow: 0 1px 0 rgba(0,0,0,0.03) inset;
  margin-bottom: 30px;
}

input[type="radio"],
input[type="checkbox"] {
  margin: 0 4px 8px 0;
}

select {
  padding: 6px;
  height: 32px;
  border-radius: 2px;
}

button {
  padding: 19px 39px 18px 39px;
  color: #FFF;
  background-color: #4bc970;
  font-size: 18px;
  text-align: center;
  font-style: normal;
  border-radius: 5px;
  width: 100%;
  border: 1px solid #3ac162;
  border-width: 1px 1px 3px;
  box-shadow: 0 -1px 0 rgba(255,255,255,0.1) inset;
  margin-bottom: 10px;
  cursor: pointer;
}

fieldset {
  margin-bottom: 30px;
  border: none;
}

legend {
  font-size: 1.4em;
  margin-bottom: 10px;
}

label {
  display: block;
  margin-bottom: 8px;
}

label.light {
  font-weight: 300;
  display: inline;
}

.number {
  background-color: #5fcf80;
  color: #fff;
  height: 30px;
  width: 30px;
  display: inline-block;
  font-size: 0.8em;
  margin-right: 4px;
  line-height: 30px;
  text-align: center;
  text-shadow: 0 1px 0 rgba(255,255,255,0.2);
  border-radius: 100%;
}

@media screen and (min-width: 480px) {

  form {
    max-width: 480px;
  }

}

        </style>
        <script type="text/javascript">
          function numbersonly(e){
            var k = event ? event.which : window.event.keyCode;
            if (k == 32) return false;
            var unicode=e.charCode? e.charCode : e.keyCode;

            if (unicode!=8) { //if the key isn't the backspace key (which we should allow)
            if (unicode<48||unicode>57) //if not a number
            return false //disable key press
        }
      }
        </script>
    </head>
    <body>
      <div class="row">
    <div class="col-md-12">
      <form action="{{ route('confirm_payment') }}" method="post">
        @csrf
        <h1> Payment Form </h1>
        
        <fieldset>
          
          <legend style="text-align: center;">Your Basic Info</legend>
        
          <label for="name">Name:</label>
          <input type="text" id="name" name="user_name" required>
        
          <label for="email">Email:</label>
          <input type="email" id="mail" name="user_email" required>
          <label for="mobile">Mobile:</label>
          <input type="text" id="mobile" name="mobile" onkeypress="return numbersonly(event)" minlength="10" maxlength="10" required>
          <label for="amount">Amount:</label>
          <input type="text" id="amount" name="amount" onkeypress="return numbersonly(event)" required>
          <input type="hidden" name="merchant_id" value="2668229">
          <input type="hidden" name="currency" value="INR">
          <input type="hidden" name="language" value="EN">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <input type="hidden" name="redirect_url" value="{{ url('response/ok') }}">
          <input type="hidden" name="cancel_url" value="{{ url('response/ok') }}">
        
        </fieldset>       
        <button type="submit">Pay Now</button>
        
       </form>
        </div>
      </div>
      
    </body>
</html>
