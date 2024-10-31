<?php
session_start();

class CommentSystem {
    private $logFile, $commentFile, $cooldownTime;

    public function __construct($logFile, $commentFile, $cooldownTime) {
        $this->logFile = $logFile;
        $this->commentFile = $commentFile;
        $this->cooldownTime = $cooldownTime;
    }

    public function logIpAddress() {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        // make the log file if it doesn't exist
        if (!file_exists($this->logFile)) {
            file_put_contents($this->logFile, '');
        }
        // get past IP addresses
        $pastAddresses = file($this->logFile, FILE_IGNORE_NEW_LINES);
        if (!in_array($ipAddress, $pastAddresses)) {
            file_put_contents($this->logFile, $ipAddress . "\n", FILE_APPEND);
            $pastAddresses[] = $ipAddress; // add new IP address to the array
        }

        return count($pastAddresses); // return total unique IP addresses
    }

    public function handleComment() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['comment'])) {
            if (isset($_SESSION['last_comment_time']) && (time() - $_SESSION['last_comment_time']) < $this->cooldownTime) {
                return "You must wait before posting another comment.";
            } else {
				if (!empty($_POST['poster'])) {
					$poster = htmlspecialchars($_POST['poster']);
				} else {
					$poster = "anonymous";
				}
                $comment = substr($_POST['comment'], 0, 400);
                file_put_contents($this->commentFile, "[" . date("Y-m-d H:i:s") . "] " . substr($poster, 0, 15) . ": " . PHP_EOL . $comment . PHP_EOL, FILE_APPEND);
                $_SESSION['last_comment_time'] = time(); //change last_comment_time to time in this session
                exit(); //end this session 
            } 
        }
        return '';
    }

    public function getComments() {
        return htmlspecialchars(file_get_contents($this->commentFile));
    }
}

//customizable paths 
$log = '../logs/ip_addresses.txt';
$commentFile = './comments.txt';
$cooldown = 30;
$commentSystem = new CommentSystem($log, $commentFile, $cooldown);

//ip logging and get user account 
$userCount = $commentSystem->logIpAddress();

//comment handling 
$message = $commentSystem->handleComment();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOME</title>
    <style>
        body {
            margin: 0;
            text-align: center;
            display: flex;
            font-family: 'MS PGothic', sans-serif;
            background-color: #290042;
            color: white;
        }
        .inline {
            display: inline;
        }
        #output {
            color: #E30A0F;
            font-size: 28px;
        }
        .main {
            flex: 3;
            padding: 20px;
        }
        .sidebar {
            flex: 0.5;
            padding: 20px;
            background-color: black;
        }
        textarea {
            height: 20px;
        }
        .divider {
            width: 1px;
            background-color: white;
        }
		.title {
			color: white; 
			font-size: 18px;
			margin: 0px;
		}
        p {
            font-size: 15px;
        }
    </style>
    <script>
        const usercount = <?php echo $userCount; ?>;

        function padding(number, length) {
            return String(number).padStart(length, '0');
        }

        window.onload = function() {
            document.getElementById('output').textContent = padding(usercount, 8) + "th";
        };
    </script>
</head>
<body>
    <div class="sidebar">
        <p>
            <a href="archive">Archive<br>
            <?php echo 500 - ((int)(disk_free_space('/') / 1000000000)) . "/500 GB used"; ?>
            </a>
        </p>
    </div>
    <div class="divider"></div>
    <div class="main">
        <h2 style="font-size: 28px; color: #FFFC07; margin: 0 0 10px;">
			HOMEPAGE @ <?php echo htmlspecialchars(file_get_contents('https://ipinfo.io/ip')); ?>
		</h2>
        <div class="usercount">
            <p class="inline">You're the</p>
            <p class="inline" id="output"></p>
            <p class="inline">user to visit!</p>
        </div>
        <br>
		<p class="title">Upload File</h2>
		<form action="upload.php" method="post" enctype="multipart/form-data">
			<input type="text" name="user" placeholder="Enter your username" required><br>
			<input type="file" name="sharex" required><br>
			<input type="text" name="secret" placeholder="Enter secret key" required><br>
			<input type="submit" value="Upload File">
		</form>
		<br>
        <p class="title">Comments</p>
        <form action="" method="post">
            <textarea name="poster" rows="1" cols="30" placeholder="Write your name here..." required></textarea><br>
            <textarea style="height: 50px;" name="comment" rows="1" cols="30" placeholder="Write your comment here..." required></textarea><br>
            <input type="submit" value="Submit Comment">
        </form>
        <br>
        <?php if ($message) echo "<p style='color: red;'>$message</p>"; ?>
        <div style="overflow-x: auto; margin: auto; white-space: nowrap; width: 700px; border: 1px solid #ccc;">
            <div style="margin: 0; padding: 0; width: 100%; max-width: 100%; text-align: left;">
                <pre style="white-space: pre-wrap; word-wrap: break-word;">
                    <?php echo $commentSystem->getComments(); ?>
                </pre>
            </div>
        </div>
    </div>
</body>
</html>
