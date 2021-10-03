<h2>Welcome <?=$_SESSION['firstname']?>!</h2> 
<p>Your access level available functions by clicking on the one you wish to interact with:</p>
<ul class="admin-links">
    <li style="font-weight: 600"><a href="employees-list">- Registered Users List</a></li>
    <li style="font-weight: 600"><a href="enquiry-admin">- Enquiries</a></li>
    <li style="font-weight: 600"><a href="shift-display">- Search Shift</a></li>
    <?php
    if ($_SESSION['access_level'] === 'admin' || $_SESSION['access_level'] === 'manager')
    {?>
        <li style="font-weight: 600"><a href="shift">- Schedule Shift</a></li>
        <li style="font-weight: 600"><a href="holiday-requests">- Holiday Requests</a></li>
    <?php
    }
    ?>

</ul>