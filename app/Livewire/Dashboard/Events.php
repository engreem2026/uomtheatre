<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Event;
use App\Models\EventLog;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('الفعاليات')]
class Events extends Component
{
    public string $title = '';
    public string $description = '';
    public string $event_date = '';
    public string $event_time = '';
    public int $editId = 0;
    public string $editTitle = '';
    public string $editDescription = '';
    public string $editDate = '';
    public string $editTime = '';
    public string $successMessage = '';
    public array $showEvent = [];

    public function createEvent()
    {
        $this->validate([
            'title'=>'required|string|max:255','description'=>'nullable|string',
            'event_date'=>'required|date|after:today','event_time'=>'nullable|string',
        ],['title.required'=>'عنوان الفعالية مطلوب','event_date.required'=>'التاريخ مطلوب','event_date.after'=>'التاريخ يجب أن يكون في المستقبل']);

        $draftStatus = Status::where('name','draft')->first();
        $event = Event::create([
            'title'=>$this->title,'description'=>$this->description,
            'event_date'=>$this->event_date,'event_time'=>$this->event_time,
            'status_id'=>$draftStatus->id,'created_by'=>Auth::id(),
        ]);
        EventLog::create(['event_id'=>$event->id,'user_id'=>Auth::id(),'old_status_id'=>null,'new_status_id'=>$draftStatus->id]);
        $this->successMessage = 'تم إنشاء الفعالية "' . $this->title . '" بنجاح';
        $this->reset(['title','description','event_date','event_time']);
        $this->dispatch('close-modal');
    }

    public function viewEvent(int $id)
    {
        $event = Event::with(['status', 'creator'])->findOrFail($id);
        $this->showEvent = [
            'title'       => $event->title,
            'description' => $event->description ?? 'لا يوجد وصف',
            'event_date'  => $event->event_date->format('Y-m-d'),
            'event_time'  => $event->event_time ?? 'غير محدد',
            'status'      => $event->status->display_name,
            'created_by'  => $event->creator->name,
            'created_at'  => $event->created_at->format('Y-m-d H:i'),
            'published_at'=> $event->published_at ? $event->published_at->format('Y-m-d H:i') : 'لم تنشر بعد',
        ];
    }

    public function openEdit(int $id)
    {
        $event = Event::findOrFail($id);
        $this->editId=$event->id; $this->editTitle=$event->title;
        $this->editDescription=$event->description??'';
        $this->editDate=$event->event_date->format('Y-m-d'); $this->editTime=$event->event_time??'';
    }

    public function updateEvent()
    {
        $this->validate(['editTitle'=>'required|string|max:255','editDate'=>'required|date']);
        Event::findOrFail($this->editId)->update([
            'title'=>$this->editTitle,'description'=>$this->editDescription,
            'event_date'=>$this->editDate,'event_time'=>$this->editTime,
        ]);
        $this->successMessage = 'تم تعديل الفعالية بنجاح';
        $this->dispatch('close-modal');
    }

    public function changeStatus(int $eventId, string $newStatusName)
    {
        $event = Event::findOrFail($eventId);
        $oldStatusId = $event->status_id;
        $newStatus = Status::where('name', $newStatusName)->first();
        $event->status_id = $newStatus->id;
        if ($newStatusName === 'published') $event->published_at = now();
        if ($newStatusName === 'closed') $event->closed_at = now();
        $event->save();
        EventLog::create(['event_id'=>$event->id,'user_id'=>Auth::id(),'old_status_id'=>$oldStatusId,'new_status_id'=>$newStatus->id]);
        $names = ['draft'=>'مسودة','added'=>'مضافة','under_review'=>'قيد المراجعة','active'=>'نشطة','published'=>'منشورة','closed'=>'مغلقة','cancelled'=>'ملغاة','end'=>'منتهية'];
        $this->successMessage = 'تم تغيير الحالة إلى: ' . ($names[$newStatusName] ?? $newStatusName);
    }

    public function deleteEvent(int $id)
    {
        $event = Event::findOrFail($id);
        $title = $event->title;
        $event->delete();
        $this->successMessage = 'تم حذف الفعالية "' . $title . '"';
    }

    public function render()
    {
        $roleName = Auth::user()->role->name;
        $allowed = ['super_admin','theater_manager','event_manager'];
        if (!in_array($roleName, $allowed)) return redirect()->route('dashboard');

        if ($roleName === 'theater_manager') {
            $events = Event::with(['status','creator'])->where('created_by', Auth::id())->orderBy('created_at','desc')->get();
        } else {
            $events = Event::with(['status','creator'])->orderBy('created_at','desc')->get();
        }

        return view('livewire.dashboard.events', ['events'=>$events, 'roleName'=>$roleName]);
    }
}
