<?php
// Menu include for admin pages. Set $active = 'dashboard'|'doctors'|'medicines'|'schedule'|'appointment'|'patient' etc before including to highlight.
if(!isset($active)) $active = '';
?>
<div class="menu">
    <table class="menu-container" border="0">
        <tr>
            <td style="padding:10px" colspan="2">
                <table border="0" class="profile-container">
                    <tr>
                        <td width="30%" style="padding-left:20px" >
                            <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                        </td>
                        <td style="padding:0px;margin:0px;">
                            <p class="profile-title">Administrator</p>
                            <p class="profile-subtitle">admin@edoc.com</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <a href="../logout.php" ><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                        </td>
                    </tr>
            </table>
            </td>
        </tr>
        <tr class="menu-row" >
            <td class="menu-btn menu-icon-dashbord <?php echo ($active==='dashboard')? 'menu-active menu-icon-dashbord-active':''; ?>" >
                <a href="index.php" class="non-style-link-menu <?php echo ($active==='dashboard')? 'non-style-link-menu-active':''; ?>"><div><p class="menu-text">Dashboard</p></a></div></a>
            </td>
        </tr>
        <tr class="menu-row">
            <td class="menu-btn menu-icon-doctor <?php echo ($active==='doctors')? 'menu-active menu-icon-doctor-active':''; ?>">
                <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">Doctors</p></a></div>
            </td>
        </tr>
        <tr class="menu-row">
            <td class="menu-btn menu-icon-dashbord <?php echo ($active==='medicines')? 'menu-active menu-icon-dashbord-active':''; ?>">
                <a href="medicines.php" class="non-style-link-menu"><div><p class="menu-text">Medicines</p></a></div>
            </td>
        </tr>
        <tr class="menu-row" >
            <td class="menu-btn menu-icon-schedule <?php echo ($active==='schedule')? 'menu-active menu-icon-schedule-active':''; ?>">
                <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Schedule</p></div></a>
            </td>
        </tr>
        <tr class="menu-row">
            <td class="menu-btn menu-icon-appoinment <?php echo ($active==='appointment')? 'menu-active menu-icon-appoinment-active':''; ?>">
                <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Appointment</p></a></div>
            </td>
        </tr>
        <tr class="menu-row" >
            <td class="menu-btn menu-icon-patient <?php echo ($active==='patient')? 'menu-active menu-icon-patient-active':''; ?>">
                <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Patients</p></a></div>
            </td>
        </tr>
    </table>
</div>
