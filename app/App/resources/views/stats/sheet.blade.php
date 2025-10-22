<tr></tr>
<tr>
    <td></td>


<?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <td></td>
        <th style="font-weight: bold; text-align: center; width: 200px; border: 1px solid black; background-color: #f2f2f2;">Отчет по филиалу</th>
        <th style="font-weight: bold; text-align: center; border: 1px solid black; background-color: #f2f2f2;">Дата создания отчета:</th>
        <th style="font-weight: bold; text-align: center; border: 1px solid black; background-color: #f2f2f2;"><strong><?php echo e($report->createdAt  ?? '-'); ?></strong></th>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</tr>
<td>Информация по филиалу</td>
<tr></tr>
<tr>
    <td></td>
    <th style="font-weight: bold; text-align: center; width: 48px; border: 1px solid black; background-color: #f2f2f2;">№</th>
    <th style="font-weight: bold; text-align: center; width: 200px; border: 1px solid black; background-color: #f2f2f2;">Название ДЭУ</th>
    <th style="font-weight: bold; text-align: center; width: 400px; border: 1px solid black; background-color: #f2f2f2;">Областной филиал</th>
    <th style="font-weight: bold; text-align: center; width: 400px; border: 1px solid black; background-color: #f2f2f2;">ФИО Ответственного</th>
    <th style="font-weight: bold; text-align: center; width: 128px; border: 1px solid black; background-color: #f2f2f2;">Должность</th>
    <th style="font-weight: bold; text-align: center; width: 128px; border: 1px solid black; background-color: #f2f2f2;">Номер телефона</th>
</tr>

<?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

    <tr>
        <td></td>
        <td style="text-align: center; width: 48px; border: 1px solid black;"><?php echo e($index+1); ?></td>
        <td style="text-align: center; width: 200px; border: 1px solid black;"><?php echo e($report->department->title ?? '-'); ?></td>
        <td style="text-align: center; width: 128px; border: 1px solid black;"><?php echo e($report->department->branch->title ?? '-'); ?></td>
        <td style="text-align: center; width: 400px; border: 1px solid black;"><?php echo e($report->department->fullName ?? '-'); ?></td>
        <td style="text-align: center; width: 128px; border: 1px solid black;"><?php echo e($report->department->position->title ?? '-'); ?></td>
        <td style="text-align: center; width: 128px; border: 1px solid black;"><?php echo e($report->department->phone ?? '-'); ?></td>
    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<tr></tr>
<td></td>
<td>Информация по рабочим</td>
<tr></tr>
<tr>
    <td></td>
    <th style="font-weight: bold; text-align: center; width: 48px; border: 1px solid black; background-color: #f2f2f2;">№</th>
    <th style="font-weight: bold; text-align: center; width: 200px; border: 1px solid black; background-color: #f2f2f2;">ФИО</th>
    <th style="font-weight: bold; text-align: center; width: 128px; border: 1px solid black; background-color: #f2f2f2;">Должность</th>
    <th style="font-weight: bold; text-align: center; width: 400px; border: 1px solid black; background-color: #f2f2f2;">Статус пользователя</th>
</tr>


<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
        <td></td>
        <td style="text-align: center; width: 48px; border: 1px solid black;"><?php echo e($index+1); ?></td>
        <td style="text-align: center; width: 200px; border: 1px solid black;"><?php echo e($user->users->fullName ?? '-'); ?></td>
        <td style="text-align: center; width: 128px; border: 1px solid black;"><?php echo e($user->users->position->title ?? '-'); ?></td>
        <td style="text-align: center; width: 400px; border: 1px solid black;"><?php echo e($user->status ?? '-'); ?></td>
    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<tr></tr>
<td></td>
<td>Информация по технике</td>
<tr></tr>
<tr>
    <td></td>
    <th style="font-weight: bold; text-align: center; width: 48px; border: 1px solid black; background-color: #f2f2f2;">№</th>
    <th style="font-weight: bold; text-align: center; width: 200px; border: 1px solid black; background-color: #f2f2f2;">Наименование техники</th>
    <th style="font-weight: bold; text-align: center; width: 200px; border: 1px solid black; background-color: #f2f2f2;">Вид</th>
    <th style="font-weight: bold; text-align: center; width: 400px; border: 1px solid black; background-color: #f2f2f2;">Гос. номер</th>
    <th style="font-weight: bold; text-align: center; width: 200px; border: 1px solid black; background-color: #f2f2f2;">Статус пользователя</th>
</tr>


<?php $__currentLoopData = $technics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $technic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

    <tr>
        <td></td>
        <td style="text-align: center; width: 48px; border: 1px solid black;"><?php echo e($index+1); ?></td>
        <td style="text-align: center; width: 200px; border: 1px solid black;"><?php echo e($technic->techniq->title ?? '-'); ?></td>
        <td style="text-align: center; width: 200px; border: 1px solid black;"><?php echo e($technic->techniq->technique->title ?? '-'); ?></td>
        <td style="text-align: center; width: 400px; border: 1px solid black;"><?php echo e($technic->techniq->number ?? '-'); ?></td>
        <td style="text-align: center; width: 200px; border: 1px solid black;"><?php echo e($technic->status ?? '-'); ?></td>
    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<tr></tr>
<td></td>
<td>Выполненные работы</td>
<tr></tr>
<tr>
    <td></td>
    <th style="font-weight: bold; text-align: center; width: 48px; border: 1px solid black; background-color: #f2f2f2;">№</th>
    <th style="font-weight: bold; text-align: center; width: 200px; border: 1px solid black; background-color: #f2f2f2;">Вид работы</th>

</tr>


<?php $__currentLoopData = $works; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $work): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

    <tr>
        <td></td>
        <td style="text-align: center; width: 48px; border: 1px solid black;"><?php echo e($index+1); ?></td>
        <td style="text-align: center; width: 400px; border: 1px solid black;"><?php echo e($work->works->content ?? '-'); ?></td>

    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>




























<tr></tr>
<tr>
    <td></td>
</tr>
<?php /**PATH C:\Users\vasil\Desktop\kaj-backend\app\App/resources/views/stats/sheet.blade.php ENDPATH**/ ?>
