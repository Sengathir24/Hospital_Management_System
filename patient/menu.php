<?php
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
                            <p class="profile-title"><?php echo htmlspecialchars($pfetch['pname'] ?? 'Patient'); ?></p>
                            <p class="profile-subtitle"><?php echo htmlspecialchars($useremail ?? ''); ?></p>
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
            <td class="menu-btn menu-icon-appoinment <?php echo ($active==='appointments')? 'menu-active menu-icon-appoinment-active':''; ?>">
                <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Appointments</p></a></div>
            </td>
        </tr>
        <tr class="menu-row">
            <!-- Use same icon as dashboard for prescriptions as requested -->
            <td class="menu-btn menu-icon-dashbord <?php echo ($active==='prescriptions')? 'menu-active menu-icon-dashbord-active':''; ?>">
                <a href="prescriptions.php" class="non-style-link-menu <?php echo ($active==='prescriptions')? 'non-style-link-menu-active':''; ?>"><div><p class="menu-text">My Prescriptions</p></a></div>
            </td>
        </tr>
        <tr class="menu-row">
            <td class="menu-btn menu-icon-schedule <?php echo ($active==='schedule')? 'menu-active menu-icon-session-active':''; ?>">
                <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Sessions</p></div></a>
            </td>
        </tr>
        <tr class="menu-row">
            <td class="menu-btn menu-icon-settings <?php echo ($active==='settings')? 'menu-active menu-icon-settings-active':''; ?>">
                <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
            </td>
        </tr>
    </table>
</div>
