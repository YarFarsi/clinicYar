<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Services\AccountingService;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(AccountingService $accounting)
    {
        return view('expenses.index', [
            'expenses' => Expense::query()->with('category')->latest('expense_date')->paginate(20),
            'categories' => ExpenseCategory::query()->orderBy('name')->get(),
            'profit' => $accounting->profit(now()->startOfMonth()->toDateString(), now()->toDateString()),
        ]);
    }

    public function store(Request $request, AccountingService $accounting)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'expense_date' => 'required|date',
            'payment_method' => 'nullable|string',
        ]);
        $accounting->createExpense($data, $request->user());

        return back()->with('ok', 'هزینه ثبت شد.');
    }
}
