<?php
    include "../config/database.php";

    //folder backup
    $backupFolder = "backup_files/";

    if(!is_dir($backupFolder)){
        mkdir($backupFolder);
    }

    //ambil list file
    $files = array_diff(scandir($backupFolder), array('.','..'));

    //hapus file
    if(isset($_GET['delete'])){
        $file = $backupFolder . $_GET['delete'];
        if(file_exists($file)){
            unlink($file);
        }
        header("Location: backup_restore.php");
        exit;
    }

    //untuk backup
    if(isset($_POST['backup'])){
        $filename = "backup_" . date("Ymd_His") . ".sql";
        $filepath = $backupFolder . $filename;

        $command = "mysqldump --user=$user --password=$pass $db > $filepath";
        system($command);

        header("Location: backup_restore.php");
        exit;
    }

    //untuk restore 
    if(isset($_POST['restore'])){
        $fileName = $_FILES['restore_file']['name'];
        $tmpPath = $_FILES['restore_file']['tmp_name'];

        if($tmpPath){
            $destination = $backupFolder . $fileName;
            move_uploaded_file($tmpPath, $destination);

            $mysqlPath = "C:/xampp/mysql/bin/mysql";
            $command = "\"$mysqlPath\" --user=$user --password=$pass $db < \"$destination\"";
            system($command);
        }

        header("Location: backup_restore.php");
        exit;
    }

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Backup & Restore - Theomart</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <style>
            body{
                margin:0;
                font-family:'Segoe UI',sans-serif;
                background: #338C91;
            }

            /*header*/
            .header{
                background: #215E61;
                padding:18px 50px;
                display:flex;
                justify-content:space-between;
                align-items:center;
                color: #F5FBE6;
            }

            .logo{
                font-size:38px;
                font-weight:bold;
                font-style:italic;
            }

            .back-btn{
                background: #F5FBE6;
                color: #215E61;
                padding:8px 18px;
                border-radius:8px;
                text-decoration:none;
                display:flex;
                align-items:center;
                gap:8px;
                font-weight:600;
            }

            .back-btn:hover{
                background: #338C91;
                color: #F5FBE6;
            }

            /*container*/
            .container{
                padding:40px 80px;
                color: #F5FBE6;
            }

            .section{
                margin-bottom:60px;
                color: #F5FBE6;
            }

            .section h2{
                font-size:26px;
                margin-bottom:20px;
                display:flex;
                align-items:center;
                gap:10px;
                color: #F5FBE6;
            }

            /*table*/
            .table-wrapper{
                background: #F5FBE6;
                border-radius:12px;
                overflow:hidden;
                box-shadow:0 10px 25px rgba(0,0,0,0.2);
            }

            table{
                width:100%;
                border-collapse:collapse;
            }

            th{
                background: #215E61;
                color: #F5FBE6;
                padding:15px;
                text-align:center;
            }

            td{
                padding:14px;
                text-align:center;
                border-bottom:1px solid #338C91;
                color: #000000;
                background: #F5FBE6;
            }


            .btn{
                padding:6px 14px;
                border-radius:6px;
                text-decoration:none;
                font-size:13px;
                color: #F5FBE6;
                margin:0 3px;
            }

            .download{
                background: #215E61;
            }

            .download:hover{
                background: #174143;
            }

            .delete{
                background: #ff0000;
            }

            .delete:hover{
                background: #920505;
            }

            /* RESTORE */
            .restore-box{
                background: #F5FBE6;
                padding:30px;
                border-radius:12px;
                width:400px;
                box-shadow:0 10px 25px rgba(0,0,0,0.2);
                color: #215E61;
            }

            .restore-box input{
                margin:15px 0;
            }

            .restore-btn{
                background: #215E61;
                color: #F5FBE6;
                padding:10px 20px;
                border:none;
                border-radius:8px;
                cursor:pointer;
            }

            .restore-btn:hover{
                background: #174143;
            }
        </style>
    </head>
    <body>
        <div class="header">
                    <div class="logo">Theomart</div>
                    <a href="dashboard.php" class="back-btn">
                        <i class="fa-solid fa-arrow-left"></i> Back
                    </a>
                </div>

        <div class="container">

            <!-- BACKUP -->
            <div class="section">
                <h2><i class="fa-solid fa-cloud-arrow-up"></i> Backup Data</h2>

                <form method="POST">
                    <button type="submit" name="backup" class="restore-btn">
                        <i class="fa-solid fa-download"></i> Create Backup
                    </button>
                </form>

                <br><br>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>Date</th>
                                <th>Size</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($files as $file): ?>
                            <tr>
                                <td><?= $file ?></td>
                                <td><?= date("d/m/Y H:i", filemtime($backupFolder.$file)) ?></td>
                                <td><?= round(filesize($backupFolder.$file)/1024,2) ?> KB</td>
                                <td>
                                    <a class="btn download" href="<?= $backupFolder.$file ?>" download>
                                        <i class="fa-solid fa-download"></i> Download
                                    </a>
                                    <a class="btn delete" href="?delete=<?= $file ?>">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- RESTORE -->
            <div class="section">
                <h2><i class="fa-solid fa-rotate-left"></i> Restore Data</h2>

                <div class="restore-box">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="file" name="restore_file" required>
                        <br>
                        <button type="submit" name="restore" class="restore-btn">
                            <i class="fa-solid fa-upload"></i> Restore Now
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </body>
</html>
