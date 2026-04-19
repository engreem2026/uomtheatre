<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Event;
use App\Models\EventLog;
use App\Models\Status;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('الفعاليات')]
class Events extends Component
{
    // حقول إنشاء فعالية
    public string $title = '';
    public string $description = '';
    public string $event_date = '';
    public string $event_time = '';
    public array $showEvent = [];

    // حقول تعديل
    public int $editId = 0;
    public string $editTitle = '';
    public string $editDescription = '';
    public string $editDate = '';
    public string $editTime = '';

    // رسائل
    public string $successMessage = '';

    // ============================================
    // إنشاء فعالية — مدير المسرح
    // ============================================
    public function createEvent()
    {
        $this->validate([
            'title'      => 'required|string|max:255',
            'description'=> 'nullable|string',
            'event_date' => 'required|date|after:today',
            'event_time' => 'nullable|string',
        ], [
            'title.required'     => 'عنوان الفعالية مطلوب',
            'event_date.required'=> 'التاريخ مطلوب',
            'event_date.after'   => 'التاريخ يجب أن يكون في المستقبل',
        ]);

        $draftStatus = Status::where('name', 'draft')->first();

        $event = Event::create([
            'title'       => $this->title,
            'description' => $this->description,
            'event_date'  => $this->event_date,
            'event_time'  => $this->event_time,
            'status_id'   => $draftStatus->id,
            'created_by'  => session('user_id'),
        ]);

        EventLog::create([
            'event_id'      => $event->id,
            'user_id'       => session('user_id'),
            'old_status_id' => null,
            'new_status_id' => $draftStatus->id,
        ]);

        $this->successMessage = 'تم إنشاء الفعالية "' . $this->title . '" بنجاح';
        $this->reset(['title', 'description', 'event_date', 'event_time']);
        $this->dispatch('close-modal');
    }

    // ============================================
    // فتح نافذة التعديل
    // ============================================
    public function openEdit(int $id)
    {
        $event = Event::findOrFail($id);
        $this->editId = $event->id;
        $this->editTitle = $event->title;
        $this->editDescription = $event->description ?? '';
        $this->editDate = $event->event_date->format('Y-m-d');
        $this->editTime = $event->event_time ?? '';
    }

    // ============================================
    // حفظ التعديلات
    // ============================================
    public function updateEvent()
    {
        $this->validate([
            'editTitle' => 'required|string|max:255',
            'editDate'  => 'required|date',
        ]);

        $event = Event::findOrFail($this->editId);
        $event->update([
            'title'       => $this->editTitle,
            'description' => $this->editDescription,
            'event_date'  => $this->editDate,
            'event_time'  => $this->editTime,
        ]);

        $this->successMessage = 'تم تعديل الفعالية بنجاح';
        $this->dispatch('close-modal');
    }

    // ============================================
    // تغيير حالة الفعالية
    // ============================================
    public function changeStatus(int $eventId, string $newStatusName)
    {
        $event = Event::findOrFail($eventId);
        $oldStatusId = $event->status_id;
        $newStatus = Status::where('name', $newStatusName)->first();

        $event->status_id = $newStatus->id;

        if ($newStatusName === 'published') {
            $event->published_at = now();
        }
        if ($newStatusName === 'closed') {
            $event->closed_at = now();
        }

        $event->save();

        EventLog::create([
            'event_id'      => $event->id,
            'user_id'       => session('user_id'),
            'old_status_id' => $oldStatusId,
            'new_status_id' => $newStatus->id,
        ]);

        $statusNames = [
            'draft' => 'مسودة', 'added' => 'مضافة', 'under_review' => 'قيد المراجعة',
            'active' => 'نشطة', 'published' => 'منشورة', 'closed' => 'مغلقة',
            'cancelled' => 'ملغاة', 'end' => 'منتهية',
        ];

        $this->successMessage = 'تم تغيير حالة الفعالية إلى: ' . ($statusNames[$newStatusName] ?? $newStatusName);
    }

    public function render()
    {
        $allowed = ['super_admin', 'theater_manager', 'event_manager'];
        if (!in_array(session('role_name'), $allowed)) {
            return redirect()->route('dashboard');
        }

        $roleName = session('role_name');

        // مدير المسرح يشوف فعالياته فقط
        if ($roleName === 'theater_manager') {
            $events = Event::with(['status', 'creator'])
                ->where('created_by', session('user_id'))
                ->orderBy('created_at', 'desc')->get();
        } else {
            // مدير الإعلام + مدير النظام يشوفون الكل
            $events = Event::with(['status', 'creator'])
                ->orderBy('created_at', 'desc')->get();
        }

        return view('livewire.dashboard.events', [
            'events'   => $events,
            'roleName' => $roleName,
        ]);
    }
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
