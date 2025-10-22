<tr></tr>
<tr>
    <td></td>
</tr>
<td>Информация по областному филиалу</td>
<tr></tr>
<tr>
    <td></td>
    <th style="font-weight: bold; text-align: center; width: 48px; border: 1px solid black; background-color: #f2f2f2;">№</th>
    <th style="font-weight: bold; text-align: center; width: 200px; border: 1px solid black; background-color: #f2f2f2;">Отчет по областному филиалу</th>
    <th style="font-weight: bold; text-align: center; width: 400px; border: 1px solid black; background-color: #f2f2f2;">Время загрузки отчета:</th>
</tr>

@foreach($branch as $index => $br)
{{--    @dd($report->department->branch->title)--}}
    <tr>
        <td></td>
        <td style="text-align: center; width: 48px; border: 1px solid black;">{{$index+1}}</td>
        <td style="text-align: center; width: 200px; border: 1px solid black;">{{$br->title?? '-'}}</td>
        <td style="text-align: center; width: 128px; border: 1px solid black;"> {{ now()->subHour()->format('H:i:s') }}</td>
    </tr>
@endforeach
<tr></tr>
<td></td>
<td>Информация по рабочим</td>
<tr></tr>
<tr>
    <td></td>
    <th style="font-weight: bold; text-align: center; width: 48px; border: 1px solid black; background-color: #f2f2f2;">№</th>
    <th style="font-weight: bold; text-align: center; width: 200px; border: 1px solid black; background-color: #f2f2f2;">ДЭУ</th>
    <th style="font-weight: bold; text-align: center; width: 200px; border: 1px solid black; background-color: #f2f2f2;">ФИО</th>
    <th style="font-weight: bold; text-align: center; width: 128px; border: 1px solid black; background-color: #f2f2f2;">Должность</th>
    <th style="font-weight: bold; text-align: center; width: 400px; border: 1px solid black; background-color: #f2f2f2;">Сделал отчет</th>
    <th style="font-weight: bold; text-align: center; width: 200px; border: 1px solid black; background-color: #f2f2f2;">Время</th>
</tr>


@foreach($users as $index => $user)
    @php
        $createdAt = \Carbon\Carbon::parse($user->report->createdAt);
    @endphp
    <tr>
        <td></td>
        <td style="text-align: center; width: 48px; border: 1px solid black;">{{$index+1}}</td>
        <td style="text-align: center; width: 200px; border: 1px solid black;">{{$user->report->department->title ?? '-'}}</td>
        <td style="text-align: center; width: 200px; border: 1px solid black;">{{$user->users->fullName ?? '-'}}</td>
        <td style="text-align: center; width: 128px; border: 1px solid black;">{{$user->users->position->title ?? '-'}}</td>
        <td style="text-align: center; width: 400px; border: 1px solid black;">{{$createdAt->subHour() ?? '-'}}</td>
        @if ($createdAt->subHour()->between(
            $createdAt->copy()->startOfDay()->addHours(6),
            $createdAt->copy()->startOfDay()->addHours(9)
        ))
            <td style="text-align: center; border: 1px solid black;">С 6:00-9:00</td>
        @elseif($createdAt->subHour()->between(
            $createdAt->copy()->startOfDay()->addHours(16),
            $createdAt->copy()->startOfDay()->addHours(18)
        ))
            <td style="text-align: center;  border: 1px solid black;">С 16:00 до 18:00 </td>)
        @else
            <td style="text-align: center;  border: 1px solid black;">{{$createdAt}} </td>
        @endif
    </tr>
@endforeach
<tr></tr>
<td></td>
<td>Информация по технике</td>
<tr></tr>
<tr>
    <td></td>
    <th style="font-weight: bold; text-align: center; width: 48px; border: 1px solid black; background-color: #f2f2f2;">№</th>
    <th style="font-weight: bold; text-align: center; width: 200px; border: 1px solid black; background-color: #f2f2f2;">ДЭУ</th>
    <th style="font-weight: bold; text-align: center; width: 200px; border: 1px solid black; background-color: #f2f2f2;">Наименование техники</th>
    <th style="font-weight: bold; text-align: center; width: 200px; border: 1px solid black; background-color: #f2f2f2;">Вид</th>
    <th style="font-weight: bold; text-align: center; width: 400px; border: 1px solid black; background-color: #f2f2f2;">Гос. номер</th>
    <th style="font-weight: bold; text-align: center; width: 200px; border: 1px solid black; background-color: #f2f2f2;">Статус пользователя</th>
    <th style="font-weight: bold; text-align: center; width: 200px; border: 1px solid black; background-color: #f2f2f2;">
</tr>


@foreach($technics as $index => $technic)
    @php
        $createdAt = \Carbon\Carbon::parse($technic->report->createdAt);
    @endphp
{{--    @dd($technic->report->department->title)--}}
    <tr>
        <td></td>
        <td style="text-align: center; width: 48px; border: 1px solid black;">{{$index+1}}</td>
        <td style="text-align: center; width: 200px; border: 1px solid black;">{{$technic->report->department->title ?? '-'}}</td>
        <td style="text-align: center; width: 200px; border: 1px solid black;">{{$technic->techniq->title?? '-'}}</td>
        <td style="text-align: center; width: 200px; border: 1px solid black;">{{$technic->techniq->technique->title?? '-'}}</td>
        <td style="text-align: center; width: 400px; border: 1px solid black;">{{$technic->techniq->number?? '-'}}</td>
        <td style="text-align: center; width: 200px; border: 1px solid black;">{{$technic->status ?? '-'}}</td>
        @if ($createdAt->subHour()->between(
            $createdAt->copy()->startOfDay()->addHours(6),
            $createdAt->copy()->startOfDay()->addHours(9)
        ))
            <td style="text-align: center; border: 1px solid black;">С 6:00-9:00</td>
        @elseif($createdAt->subHour()->between(
            $createdAt->copy()->startOfDay()->addHours(16),
            $createdAt->copy()->startOfDay()->addHours(18)
        ))
            <td style="text-align: center;  border: 1px solid black;">С 16:00 до 18:00 </td>)
        @else
            <td style="text-align: center;  border: 1px solid black;">{{$createdAt}} </td>
        @endif
    </tr>
@endforeach
<tr></tr>
<td></td>
<td>Выполненные работы</td>
<tr></tr>
<tr>
    <td></td>
    <th style="font-weight: bold; text-align: center; width: 48px; border: 1px solid black; background-color: #f2f2f2;">№</th>
    <th style="font-weight: bold; text-align: center; width: 200px; border: 1px solid black; background-color: #f2f2f2;">ДЭУ</th>
    <th style="font-weight: bold; text-align: center; width: 200px; border: 1px solid black; background-color: #f2f2f2;">Вид работы</th>
    <th style="font-weight: bold; text-align: center; width: 200px; border: 1px solid black; background-color: #f2f2f2;">
</tr>


@foreach($works as $index => $work)
    @php
        $createdAt = \Carbon\Carbon::parse($work->report->createdAt);
    @endphp
{{--        @dd($work)--}}
    <tr>
        <td></td>
        <td style="text-align: center; width: 48px; border: 1px solid black;">{{$index+1}}</td>
        <td style="text-align: center; width: 450px; border: 1px solid black;">{{$work->report->department->title ?? '-'}}</td>
        <td style="text-align: center; width: 200px; border: 1px solid black;">{{$work->works->content?? '-'}}</td>
        @if ($createdAt->subHour()->between(
            $createdAt->copy()->startOfDay()->addHours(6),
            $createdAt->copy()->startOfDay()->addHours(9)
        ))
            <td style="text-align: center; border: 1px solid black;">С 6:00-9:00</td>
        @elseif($createdAt->subHour()->between(
            $createdAt->copy()->startOfDay()->addHours(16),
            $createdAt->copy()->startOfDay()->addHours(18)
        ))
            <td style="text-align: center;  border: 1px solid black;">С 16:00 до 18:00 </td>)
        @else
            <td style="text-align: center;  border: 1px solid black;">{{$createdAt}} </td>
        @endif

    </tr>
@endforeach
{{--@foreach($reports as $report)--}}
{{--    @foreach($report->users as $index => $user)--}}
{{--        <td style="text-align: center; width: 400px; border: 1px solid black;">{{$user->fullName}}</td>--}}
{{--    @endforeach--}}
{{--@endforeach--}}
{{--@dd($users)--}}
{{--@foreach($users as $user)--}}

{{--    @foreach($user->users as $index )--}}
{{--        @dd($user->users->position)--}}
{{--    <tr>--}}
{{--        <td></td>--}}
{{--        <td style="text-align: center; width: 48px; border: 1px solid black;">{{$index+1}}</td>--}}
{{--        <td style="text-align: center; width: 400px; border: 1px solid black;">{{$user->fullName}}</td>--}}
{{--        <td style="text-align: center; width: 128px; border: 1px solid black;">{{$user->users->position->title}}</td>--}}
{{--        <td style="text-align: center; width: 128px; border: 1px solid black;">{{\Illuminate\Support\Arr::get($user, 'departmentName')}}</td>--}}
{{--        <td style="text-align: center; width: 96px; border: 1px solid black;">{{$user['enteredAt'] ? $user['enteredAt']->setTimezone('Asia/Aqtobe')->format('d.m.Y H:i') : '-'}}</td>--}}
{{--        <td style="text-align: center; width: 96px; border: 1px solid black;">{{$user['leaveAt'] ? $user['leaveAt']->setTimezone('Asia/Aqtobe')->format('d.m.Y H:i') : '-'}}</td>--}}
{{--        <td style="text-align: center; width: 128px; border: 1px solid black;">{{$user['hourCount'] ?: '-'}}</td>--}}
{{--        <td style="text-align: center; width: 128px; border: 1px solid black;">{{$user['plan']}}</td>--}}

{{--        <!-- Добавляем столбец для выполненных работ -->--}}
{{--        <td style="text-align: center; width: 128px; border: 1px solid black;">--}}
{{--            {{ implode(', ', $userinfo['works'] ?? []) }}--}}
{{--        </td>--}}
{{--    </tr>--}}
{{--    @endforeach--}}
{{--@endforeach--}}
<tr></tr>
<tr>
    <td></td>
</tr>
