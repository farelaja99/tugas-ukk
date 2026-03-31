<?php
    session_start();
    include "../config/database.php";

    //hapus produk
    if(isset($_GET['delete'])){
        $id = $_GET['delete'];

        $get = mysqli_query($conn,"SELECT photo FROM products WHERE id='$id'");
        $data = mysqli_fetch_assoc($get);

        if($data && $data['photo'] != ''){
            unlink("../uploads/".$data['photo']);
        }

        mysqli_query($conn,"DELETE FROM products WHERE id='$id'");
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }

    //tambah produk
    if(isset($_POST['name'])){

        $id       = $_POST['id'];
        $name     = $_POST['name'];
        $price    = $_POST['price'];
        $category = $_POST['category'];
        $stock    = $_POST['stock'];

        $photoName = $_FILES['photo']['name'];
        $tmpName   = $_FILES['photo']['tmp_name'];

        //update produk
        if($id != ''){

            if($photoName != ''){

                $getOld = mysqli_query($conn,"SELECT photo FROM products WHERE id='$id'");
                $oldData = mysqli_fetch_assoc($getOld);

                if($oldData['photo'] != ''){
                    unlink("../uploads/".$oldData['photo']);
                }

                $newName = time()."_".$photoName;
                move_uploaded_file($tmpName,"../uploads/".$newName);

                mysqli_query($conn,"UPDATE products SET
                    name='$name',
                    category='$category',
                    price='$price',
                    stock='$stock',
                    photo='$newName'
                    WHERE id='$id'
                ");

            }else{

                mysqli_query($conn,"UPDATE products SET
                    name='$name',
                    category='$category',
                    price='$price',
                    stock='$stock'
                    WHERE id='$id'
                ");
            }

        }
        //masukan foto baru
        else{

            if($photoName != ''){
                $newName = time()."_".$photoName;
                move_uploaded_file($tmpName,"../uploads/".$newName);
            }else{
                $newName = '';
            }

            mysqli_query($conn,"INSERT INTO products
                (name,category,price,stock,photo)
                VALUES
                ('$name','$category','$price','$stock','$newName')
            ");
        }

        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }

    //edit
    $editData = null;

    if(isset($_GET['edit'])){
        $idEdit = $_GET['edit'];
        $result = mysqli_query($conn,"SELECT * FROM products WHERE id='$idEdit'");
        $editData = mysqli_fetch_assoc($result);
    }

    //ambil data
    $data = mysqli_query($conn,"SELECT * FROM products ORDER BY id DESC");
?>


<!DOCTYPE html>
<html>
<head>
<title>Product Management</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background: #338C91;
}

/* HEADER */
.header{
    background:#215E61;
    padding:18px 50px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    color:white;
}

.logo{
    font-size:38px;
    font-weight:bold;
    font-style:italic;
}

.back-btn{
    background:#F5FBE6;
    color:#1b4e50;
    padding:8px 18px;
    border-radius:8px;
    text-decoration:none;
    display:flex;
    align-items:center;
    gap:8px;
    font-weight:600;
}

/* CONTENT */
.container{
    padding:40px 60px;
    color:white;
}

.title-row{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.title-row h2{
    font-size:30px;
    margin:0;
    color:#F5FBE6;
}

.btn-add{
    background:#215E61;
    padding:10px 20px;
    border-radius:8px;
    cursor:pointer;
    font-weight:600;
    display:flex;
    align-items:center;
    gap:8px;
    color:#F5FBE6;
}

/* TABLE */
.table-wrapper{
    background: #e5e9d5;
    border-radius:12px;
    overflow:hidden;
}

table{
    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
}

th{
    background: #215E61;
    color:white;
    padding:15px;
    text-align:center;
}

td{
    padding:15px;
    text-align:center;
    border-bottom:1px solid #ccc;
    color:#215E61;
}

td img{
    width:60px;
    border-radius:8px;
}

.action-btn{
    padding:6px 12px;
    border-radius:6px;
    font-size:13px;
    text-decoration:none;
    color:white;
    margin:0 3px;
}

.edit{
    background:#2f6f71;
}

.delete{
    background:#c0392b;
}

/* MODAL */
.modal{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.6);
    display:flex;
    justify-content:center;
    align-items:center;
    opacity:0;
    pointer-events:none;
    transition:0.3s;
}

.modal.active{
    opacity:1;
    pointer-events:auto;
}

.modal-content{
    background:#338C91;
    width:600px;
    padding:30px;
    border-radius:12px;
    color:white;
    transform:scale(0.9);
    transition:0.3s;
}

.modal.active .modal-content{
    transform:scale(1);
}

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}

.form-group{
    display:flex;
    flex-direction:column;
}

.form-group input,
.form-group select{
    padding:10px;
    border-radius:8px;
    border:none;
    margin-top:5px;
}

.full{
    grid-column:span 2;
}

.modal-footer{
    margin-top:20px;
    text-align:center;
}

.modal-footer button{
    background:#144244;
    padding:10px 30px;
    border:none;
    border-radius:8px;
    color:white;
    font-weight:600;
    cursor:pointer;
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

    <div class="title-row">
        <h2>Product Management</h2>
        <div class="btn-add" onclick="openModal('add')">
            <i class="fa-solid fa-plus"></i> Add Product
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            <?php while($row=mysqli_fetch_assoc($data)) { ?>
                <tr>
                    <td>
                        <img src="../uploads/<?= $row['photo']; ?>"><br>
                        <?= $row['name']; ?>
                    </td>
                    <td><?= $row['category']; ?></td>
                    <td>Rp <?= number_format($row['price'],0,',','.'); ?></td>
                    <td><?= $row['stock']; ?></td>
                    <td>
                        <a class="action-btn edit"
                            href="?edit=<?= $row['id']; ?>">
                            <i class="fa-solid fa-pen"></i>
                        </a>


                        <a class="action-btn delete"
                           href="?delete=<?= $row['id']; ?>">
                           <i class="fa-solid fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php } ?>

            </tbody>
        </table>
    </div>
</div>

<!-- MODAL -->
<div class="modal" id="productModal">
    <div class="modal-content">
        <h2 id="modalTitle">Add Product</h2>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $editData['id'] ?? '' ?>">

            <div class="form-grid">

                <div class="form-group full">
                    <label>Name Product</label>
                    <input type="text" name="name" required
                    value="<?= $editData['name'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label>Price</label>
                    <input type="number" name="price" required
                    value="<?= $editData['price'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label>Select Category</label>
                    <select name="category">
                        <option <?= (isset($editData['category']) && $editData['category']=='T-shirt')?'selected':''; ?>>T-shirt</option>
                        <option <?= (isset($editData['category']) && $editData['category']=='Hoodie')?'selected':''; ?>>Hoodie</option>
                        <option <?= (isset($editData['category']) && $editData['category']=='Jacket')?'selected':''; ?>>Jacket</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Stock</label>
                    <input type="number" name="stock" required
                    value="<?= $editData['stock'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label>Add Photo</label>
                    <input type="file" name="photo">
                </div>

            </div>

            <div class="modal-footer">
                <button type="submit">
                    <?= isset($editData) ? 'Update Product' : 'Save Product'; ?>
                </button>
            </div>

        </form>

    </div>
</div>

<script>

function openModal(type,id=null){
    document.getElementById('productModal').classList.add('active');

    if(type === 'edit'){
        document.getElementById('modalTitle').innerText = "Edit Product";
        // nanti lo isi ajax ambil data by id kalau mau dinamis
    }else{
        document.getElementById('modalTitle').innerText = "Add Product";
    }
}

window.onclick = function(e){
    const modal = document.getElementById('productModal');
    if(e.target == modal){
        modal.classList.remove('active');
    }
}

</script>
<?php if(isset($editData) && $editData != null) { ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById('productModal').classList.add('active');
    document.getElementById('modalTitle').innerText = "Edit Product";
});
</script>
<?php } ?>


</body>
</html>
