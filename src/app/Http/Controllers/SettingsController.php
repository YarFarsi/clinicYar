<?php

namespace App\Http\Controllers;

use App\Enums\OperationMode;
use App\Models\ClinicNotification;
use App\Services\BackupService;
use App\Services\SearchService;
use App\Services\SyncService;
use App\Services\WordPressService;
use App\Support\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index', ['clinic' => Tenant::requireClinic()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'timezone' => 'required|string',
            'currency' => 'required|string',
            'operation_mode' => 'required|string',
        ]);
        $clinic = Tenant::requireClinic();
        $clinic->update([
            ...$data,
            'operation_mode' => OperationMode::from($data['operation_mode']),
        ]);

        return back()->with('ok', 'تنظیمات ذخیره شد.');
    }

    public function backup(BackupService $backups)
    {
        return view('settings.backup', [
            'backups' => \App\Models\Backup::query()->latest()->get(),
        ]);
    }

    public function createBackup(Request $request, BackupService $backups)
    {
        $backups->create($request->user());

        return back()->with('ok', 'پشتیبان تهیه شد.');
    }

    public function downloadBackup(\App\Models\Backup $backup)
    {
        $path = storage_path('app/'.$backup->path);
        abort_unless(is_file($path), 404);

        return response()->download($path);
    }

    public function integrations()
    {
        return view('settings.integrations', ['clinic' => Tenant::requireClinic()]);
    }

    public function saveWordpress(Request $request, WordPressService $wp)
    {
        $data = $request->validate([
            'site_url' => 'required|url',
            'username' => 'required|string',
            'application_password' => 'required|string',
        ]);
        $wp->saveCredentials(Tenant::requireClinic(), $data['site_url'], $data['username'], $data['application_password']);

        return back()->with('ok', 'اتصال وردپرس ذخیره شد. رمز اصلی وردپرس ذخیره نمی‌شود.');
    }

    public function search(Request $request, SearchService $search)
    {
        return view('search.index', [
            'q' => $request->string('q')->toString(),
            'results' => $search->global($request->string('q')->toString()),
        ]);
    }

    public function notifications()
    {
        return view('notifications.index', [
            'items' => ClinicNotification::query()->latest()->limit(50)->get(),
        ]);
    }

    public function connectivity()
    {
        return response()->json([
            'success' => true,
            'data' => ['local' => true, 'internet' => false],
            'message' => null,
            'errors' => [],
        ]);
    }

    public function sync(SyncService $sync)
    {
        $count = $sync->processDue();

        return back()->with('ok', "{$count} عملیات همگام شد.");
    }

    public function users()
    {
        $clinic = Tenant::requireClinic();

        return view('settings.users', [
            'users' => $clinic->users()->get(),
        ]);
    }
}
