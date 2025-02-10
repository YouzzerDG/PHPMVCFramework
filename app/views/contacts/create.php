<form action="<?= \App\Uri::get(); ?>" method="post">
    <input type="hidden" name="hmn" value="true">
    <div>
        <label for="contact_first_name">First name</label>
        <input type="text" name="contact_first_name" id="contact_first_name">
    </div>
    <div>
        <label for="contact_last_name">Last name</label>
        <input type="text" name="contact_last_name" id="contact_last_name">
    </div>
    <div>
        <label for="contact_email">E-mail</label>
        <input type="text" name="contact_email" id="contact_email">
    </div>
    <div>
        <label for="contact_phone_number">Phone number</label>
        <input type="tel" name="contact_phone_number" id="contact_phone_number">
    </div>
    <input type="submit" value="Submit">
</form>