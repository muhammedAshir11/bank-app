@extends('layouts.app')
@section('content')
    <section class="section" id="homeSection">
        <x-card>
            <div class="card-header">Welcome {{ Auth::user()->name }}</div>
            <div class="card-body">
                <table>
                    <tr>
                        <td>YOUR ID: </td>
                        <td>{{ Auth::user()->email }}</td>
                    </tr>
                    <tr>
                        <td>YOUR BALANCE: </td>
                        <td id="balanceAmount">{{ number_format(Auth::user()->getBalance(), 2) }}</td>
                    </tr>
                </table>
            </div>
        </x-card>
    </section>

    <section class="section" id="depositSection">
        <x-card>
            <div class="card-header">Deposit Money</div>

            <div class="card-body">
                <form id="depositForm" data-url="{{ route('amount.deposit') }}">
                    @csrf
                    <div class="form-group">
                        <label for="depositAmount">Amount</label>
                        <input class="form-control" type="text" id="depositAmount" name="depositAmount"
                            placeholder="Enter amount to deposit" />
                        <span class="hidden" id="depositMsg"></span>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary" type="button"
                            onclick="transactions('deposit',this)">Deposit</button>
                    </div>
                </form>
            </div>
        </x-card>
    </section>


    <section class="section" id="withdrawSection">
        <x-card>
            <div class="card-header">Withdraw Money</div>

            <div class="card-body">
                <form id="withdrawForm" data-url="{{ route('amount.withdraw') }}">
                    @csrf
                    <div class="form-group">
                        <label for="withdrawAmount">Amount</label>
                        <input class="form-control" type="text" id="withdrawAmount" name="withdrawAmount"
                            placeholder="Enter amount to withdraw" />
                        <span class="hidden" id="withdrawMsg"></span>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary" type="button"
                            onclick="transactions('withdraw',this)">Withdraw</button>
                    </div>
                </form>
            </div>
        </x-card>
    </section>

    <section class="section" id="transferSection">
        <x-card>
            <div class="card-header">Transfer Money</div>

            <div class="card-body">
                <div id="transferMsg" class="alert alert-success hidden" style="background-color: #fff;" role="alert">
                </div>
                <form id="transferForm" data-url="{{ route('amount.transfer') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input class="form-control" type="text" id="email" name="email"
                            placeholder="Enter email" />

                        <label for="transferAmount">Amount</label>
                        <input class="form-control" type="text" id="transferAmount" name="transferAmount"
                            placeholder="Enter amount to transfer" />
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary" type="button"
                            onclick="transactions('transfer',this)">Transfer</button>
                    </div>
                </form>
            </div>
        </x-card>
    </section>

    <section class="section" id="statementSection">
        <x-card>
            <div class="card-header">Statement of account</div>

            <div class="card-body">
                <div id="statementData"></div>
                <div id="paginationLinks"></div>
            </div>
        </x-card>
    </section>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js">
    </script>
    <script src="{{ asset('js/home.js') }}"></script>
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">
@endsection
