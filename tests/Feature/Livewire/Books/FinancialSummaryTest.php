<?php

namespace Tests\Feature\Livewire\Books;

use App\Http\Livewire\Books\FinancialSummary;
use App\Models\Book;
use App\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinancialSummaryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_see_empty_book_financial_summary_card_without_budget()
    {
        $book = factory(Book::class)->create();
        $startOfWeekDayDate = now()->startOfWeek()->isoFormat('dddd, D MMM Y');
        $todayDayDate = now()->isoFormat('dddd, D MMM Y');

        Livewire::test(FinancialSummary::class, ['bookId' => $book->id])
            ->assertSeeHtml('<span id="start_periode_label">'.__('report.balance_per_date', ['date' => $startOfWeekDayDate]).'</span>')
            ->assertSeeHtml('<span id="start_periode_balance">'.format_number(0).'</span>')
            ->assertSeeHtml('<span id="current_periode_spending_total">'.format_number(0).'</span>')
            ->assertSeeHtml('<span id="current_balance_label">'.__('report.today_balance', ['date' => $todayDayDate]).'</span>')
            ->assertSeeHtml('<span id="current_balance">'.format_number(0).'</span>');
    }

    /** @test */
    public function user_can_see_empty_book_financial_summary_card_with_budget()
    {
        $book = factory(Book::class)->create(['budget' => 1000000]);

        Livewire::test(FinancialSummary::class, ['bookId' => $book->id])
            ->assertSeeHtml('<span id="current_periode_budget">'.format_number(1000000).'</span>')
            ->assertSeeHtml('<span id="current_periode_income_total">'.format_number(0).'</span>')
            ->assertSeeHtml('<span id="current_periode_spending_total">'.format_number(0).'</span>')
            ->assertSeeHtml('<span id="current_balance">'.format_number(0).'</span>')
            ->assertSeeHtml('<span id="current_periode_budget_remaining">'.format_number(1000000).'</span>');
    }

    /** @test */
    public function user_can_see_book_financial_summary_card_without_budget()
    {
        $book = factory(Book::class)->create();
        $today = today()->format('Y-m-d');

        factory(Transaction::class)->create([
            'amount' => 100000,
            'date' => $today,
            'in_out' => 1,
        ]);
        factory(Transaction::class)->create([
            'amount' => 10000,
            'date' => $today,
            'in_out' => 0,
        ]);

        Livewire::test(FinancialSummary::class, ['bookId' => $book->id])
            ->assertSeeHtml('<span id="start_periode_balance">'.format_number(0).'</span>')
            ->assertSeeHtml('<span id="current_periode_income_total">'.format_number(100000).'</span>')
            ->assertSeeHtml('<span id="current_periode_spending_total">'.format_number(-10000).'</span>')
            ->assertSeeHtml('<span id="current_balance">'.format_number(90000).'</span>');
    }

    /** @test */
    public function user_can_see_book_financial_summary_card_with_budget()
    {
        $book = factory(Book::class)->create(['budget' => 1000000]);
        $today = today()->format('Y-m-d');

        factory(Transaction::class)->create([
            'amount' => 100000,
            'date' => $today,
            'in_out' => 1,
        ]);
        factory(Transaction::class)->create([
            'amount' => 10000,
            'date' => $today,
            'in_out' => 0,
        ]);

        Livewire::test(FinancialSummary::class, ['bookId' => $book->id])
            ->assertSeeHtml('<span id="current_periode_budget">'.format_number(1000000).'</span>')
            ->assertSeeHtml('<span id="current_periode_income_total">'.format_number(100000).'</span>')
            ->assertSeeHtml('<span id="current_periode_spending_total">'.format_number(-10000).'</span>')
            ->assertSeeHtml('<span id="current_balance">'.format_number(90000).'</span>')
            ->assertSeeHtml('<span id="current_periode_budget_remaining">'.format_number(900000).'</span>');
    }

    /** @test */
    public function make_sure_other_books_transactions_are_not_calculated()
    {
        $book = factory(Book::class)->create();
        $today = today()->format('Y-m-d');

        factory(Transaction::class)->create([
            'amount' => 100000,
            'date' => $today,
            'book_id' => $book->id,
            'in_out' => 1,
        ]);
        factory(Transaction::class)->create([
            'amount' => 10000,
            'date' => $today,
            'book_id' => $book->id,
            'in_out' => 0,
        ]);
        $anotherBook = factory(Book::class)->create();
        factory(Transaction::class)->create([
            'amount' => 35000,
            'date' => $today,
            'book_id' => $anotherBook->id,
            'in_out' => 1,
        ]);

        Livewire::test(FinancialSummary::class, ['bookId' => $book->id])
            ->assertSeeHtml('<span id="start_periode_balance">'.format_number(0).'</span>')
            ->assertSeeHtml('<span id="current_periode_income_total">'.format_number(100000).'</span>')
            ->assertSeeHtml('<span id="current_periode_spending_total">'.format_number(-10000).'</span>')
            ->assertSeeHtml('<span id="current_balance">'.format_number(90000).'</span>');
    }

    /** @test */
    public function make_sure_start_periode_balance_is_calculated_from_the_previous_periode_ending_balance()
    {
        $book = factory(Book::class)->create();
        $lastWeekDate = now()->subWeek()->format('Y-m-d');
        $today = today()->format('Y-m-d');

        factory(Transaction::class)->create([
            'amount' => 100000,
            'date' => $today,
            'book_id' => $book->id,
            'in_out' => 1,
        ]);
        factory(Transaction::class)->create([
            'amount' => 10000,
            'date' => $today,
            'book_id' => $book->id,
            'in_out' => 0,
        ]);
        factory(Transaction::class)->create([
            'amount' => 99000,
            'date' => $lastWeekDate,
            'book_id' => $book->id,
            'in_out' => 1,
        ]);
        factory(Transaction::class)->create([
            'amount' => 10000,
            'date' => $lastWeekDate,
            'book_id' => $book->id,
            'in_out' => 0,
        ]);

        Livewire::test(FinancialSummary::class, ['bookId' => $book->id])
            ->assertSeeHtml('<span id="start_periode_balance">'.format_number(89000).'</span>')
            ->assertSeeHtml('<span id="current_periode_income_total">'.format_number(100000).'</span>')
            ->assertSeeHtml('<span id="current_periode_spending_total">'.format_number(-10000).'</span>')
            ->assertSeeHtml('<span id="current_balance">'.format_number(179000).'</span>');
    }

    /** @test */
    public function make_sure_transactions_from_next_periode_are_not_calculated()
    {
        $book = factory(Book::class)->create();
        $nextWeekDate = now()->addWeek()->format('Y-m-d');
        $today = today()->format('Y-m-d');

        factory(Transaction::class)->create([
            'amount' => 100000,
            'date' => $today,
            'book_id' => $book->id,
            'in_out' => 1,
        ]);
        factory(Transaction::class)->create([
            'amount' => 10000,
            'date' => $today,
            'book_id' => $book->id,
            'in_out' => 0,
        ]);
        factory(Transaction::class)->create([
            'amount' => 99000,
            'date' => $nextWeekDate,
            'book_id' => $book->id,
            'in_out' => 0,
        ]);

        Livewire::test(FinancialSummary::class, ['bookId' => $book->id])
            ->assertSeeHtml('<span id="start_periode_balance">'.format_number(0).'</span>')
            ->assertSeeHtml('<span id="current_periode_income_total">'.format_number(100000).'</span>')
            ->assertSeeHtml('<span id="current_periode_spending_total">'.format_number(-10000).'</span>')
            ->assertSeeHtml('<span id="current_balance">'.format_number(90000).'</span>');
    }
}
