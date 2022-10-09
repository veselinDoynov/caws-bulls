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
            <h2>Welcome player <span style="font-size: large;color:saddlebrown"> ({{$gameId}}) </span></h2>
            <h3 class="-mb-2">Let's play game caws and bulls ... good luck :) </h3>

            <p hidden><span>Number is: {{$sequence}}</span></p>

            <form>
                <input id="numberSuggested" class="form-control small" type="text" placeholder="Your suggestion here"
                       pattern="[0-9]{4}"/>
                <p style="color:silver" id="current_suggestions"></p>
                <hr>
                <button id="submitRound" class="btn btn-success">Shoot</button>
                <button id="newGame" class="btn btn-success" style="float: right;">Start new game</button>
                <br/>
                <br/>
                <p style="color:red" hidden id="error"></p>
                <p hidden id="loading">Calculating ... </p>
                <p style="color:orange; font-size: medium" hidden id="currentResult"></p>
                <p hidden id="gameWin"> You win !!!</p>
            </form>
            @if (count($topResults))
                <h2> Top Results </h2>
                <div id="results">

                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Player</th>
                            <th scope="col">Succeed after attempts</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($topResults as $player => $topResult)
                            <tr>
                                <td>{{$topResult['date']}}</td>
                                <td>{{$player}}</td>
                                <td>{{$topResult['result']}}</td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
</body>


<script type="text/javascript">
    $(document).ready(function () {

        $('#newGame').hide();
        suggestions = [];
        $("#submitRound").on('click', function (e) {
            e.preventDefault();
            gameOn();
        });

        $("#newGame").on('click', function (e) {
            window.location.reload();
        });


        function gameOn() {

            $('#loading').show();
            $('#currentResult').html('');
            $('#error').hide();

            $.ajax({
                type: 'POST',
                url: '{{ route('game') }}',
                data: {
                    'suggestion': $("#numberSuggested").val(),
                    '_token': '{{ csrf_token()}}',
                    'gameId': '{{ $gameId }}',
                    'sequence': '{{ $sequence }}'
                },
                dataType: 'json',
                success: function (data) {
                    if (data.error) {
                        $('#loading').hide();
                        $('#error').show().html('Error ' + data.error);
                        return;
                    }
                    $('#loading').hide();
                    $('#currentResult').html('');
                    $('#currentResult').show();
                    suggestions.push($("#numberSuggested").val() + ' caws:' + data.caws + ' bulls:' + data.bulls + ' ');
                    $("#current_suggestions").html('Your suggestions so far: ( ' + String(suggestions)+ ' )');
                    if (data.gameComplete == 1) {
                        $('#gameWin').show().html('You win !! ( with ' + suggestions.length + ' attempts )');
                        $('#submitRound').hide();
                        $('#newGame').show();
                        return;
                    }
                    
                    $('#currentResult').html('You have ' + data.bulls + ' bulls and ' + data.caws + ' caws');


                },
                error: function (data) {
                    $('#currentResult').hide();
                    $('#loading').hide();
                    $('#error').show().html('Server error');
                }
            });
        }

    });
</script>
</html>