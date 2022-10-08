<!doctype html>
<html>

<head>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
    <meta charset="utf-8">
    <title>Laravel</title>
</head>

<body>
<div class="container">
    <div class="row">
        <div class="well">
            <h1 class="-mb-2">Game</h1>

            <p hidden><span>Number is: {{$sequence}}</span></p>

            <form>
                <input id="numberSuggested" class="form-control small" type="text" placeholder="Your suggestion here" pattern="[0-9]{4}"/>
                <hr>
                <button id="submitRound" class="btn btn-success">Try </button>
                <br/>
                <p style="color:red" hidden id="error"> </p>
                <p hidden id="loading">Calculating ... </p>
                <p hidden id="currentResult"></p>
                <p hidden id="gameWin"> You win !!!</p>
            </form>
        </div>
    </div>
</div>
</body>


<script type="text/javascript">
    $(document).ready(function() {

        $("#submitRound").on('click', function(e){

            e.preventDefault();
            $('#loading').show();
            $('#currentResult').html('');
            $('#error').hide();

            $.ajax({
                type: 'POST',
                url: '{{ route('game') }}',
                data: {
                    'suggestion': $("#numberSuggested").val() ,
                    '_token':'{{ csrf_token()}}',
                    'gameId':'{{ $gameId }}',
                    'sequence':'{{ $sequence }}'
                },
                dataType: 'json',
                success: function(data) {
                    if(data.error) {
                        $('#loading').hide();
                        $('#error').show().html('Error ' + data.error);
                        return;
                    }
                    $('#loading').hide();
                    $('#currentResult').html('');
                    $('#currentResult').show();
                    if(data.gameComplete == 1) {
                        $('#gameWin').show();
                        return;
                    }
                    $('#currentResult').html('You have ' + data.bulls + ' bulls and '+ data.caws + ' caws');


                },
                error:function(data){
                    $('#currentResult').hide();
                    $('#loading').hide();
                    $('#error').show().html('Server error');
                }
            });
        });


    });
</script>
</html>