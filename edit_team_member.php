<!-- edit_team_member.php -->
<?php
$id = $_GET['id'];
$team_member = $connection->query("SELECT * FROM team_members WHERE id = $id")->fetch_assoc();
?>
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $team_member['id']; ?>">
    <label for="name">ชื่อ:</label>
    <input type="text" id="name" name="name" value="<?php echo $team_member['name']; ?>" required>
    <label for="description">คำอธิบาย:</label>
    <textarea id="description" name="description" required><?php echo $team_member['description']; ?></textarea>
    <label for="image">รูปภาพ:</label>
    <input type="file" id="image" name="image" accept="image/*">
    <input type="hidden" name="current_image" value="<?php echo $team_member['image_path']; ?>">
    <input type="hidden" name="update_team_member" value="1">
    <input type="submit" value="บันทึกการแก้ไข">
</form>
