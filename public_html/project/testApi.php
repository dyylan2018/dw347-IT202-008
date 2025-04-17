<?php
require(__DIR__ . "/../../partials/nav.php");

$result = [];
if (isset($_GET["player"])) {
    //function=GLOBAL_QUOTE&symbol=MSFT&datatype=json
    $data = ["function" => "GLOBAL_QUOTE", "player" => $_GET["player"], "datatype" => "json"];
    $endpoint = "https://therundown-therundown-v1.p.rapidapi.com/v2/teams/48/players";
    $isRapidAPI = true;
    $rapidAPIHost = "therundown-therundown-v1.p.rapidapi.com";
    $result = get($endpoint, "STOCK_API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    //example of cached data to save the quotas, don't forget to comment out the get() if using the cached data for testing
    /* $result = ["status" => 200, "response" => '{
    "Global Quote": {
        "01. symbol": "MSFT",
        "02. open": "420.1100",
        "03. high": "422.3800",
        "04. low": "417.8400",
        "05. price": "421.4400",
        "06. volume": "17861855",
        "07. latest trading day": "2024-04-02",
        "08. previous close": "424.5700",
        "09. change": "-3.1300",
        "10. change percent": "-0.7372%"
    }
}'];*/
    error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }
}
?>
<div class="d-flex justify-content-center align-items-start pt-5" style="min-height: 80vh;">
    <div class="container text-center">
        <h1 class="mb-3">Player Stats</h1>
        <p class="mb-4">
            Search any NY Yankees player to see their stats
        </p>
        <form>
            <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap mb-3">
                <label for="player" class="form-label m-0 align-self-center">Player</label>
                <input name="player" id="player" class="form-control w-auto" />
                <input type="submit" value="Fetch Player" class="btn btn-primary" />
            </div>
        </form>
        <div class="row justify-content-center">
            <?php if (isset($result)) : ?>
                <?php foreach ($result as $stock) : ?>
                    <div class="col-12 col-md-8">
                        <pre><?php var_export($stock); ?></pre>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
require(__DIR__ . "/../../partials/flash.php");