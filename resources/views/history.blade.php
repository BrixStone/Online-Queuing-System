@extends('layout')

@section('content')

<style>
    .dashboard-container {
        display: flex;
        gap: 20px;
        max-width: 1000px;
        margin: 20px auto;
        color: white;
        font-family: sans-serif;
    }

    /* LEFT SIDEBAR */
    .sidebar-left {
        flex: 1;
        background-color: #1e293b;
        padding: 20px;
        border-radius: 8px;
        height: 80vh;
        overflow-y: auto;
    }

    /* RIGHT CONTENT */
    .content-right {
        flex: 2.5;
        background-color: #1e293b;
        padding: 40px 30px;
        border-radius: 8px;
    }

    /* CLICKABLE TICKET */
    .ticket-link {
        text-decoration: none;
        color: inherit;
        display: block;
        margin-bottom: 12px;
    }

    /* TICKET CARD */
    .ticket-card {
        background-color: #334155;
        padding: 15px;
        border-radius: 8px;
        transition: 0.2s;
        border: 2px solid transparent;
    }

    .ticket-card:hover {
        border-color: #94a3b8;
    }

    .ticket-card.active {
        border-color: #60a5fa;
        background-color: #0f172a;
    }

    /* STATUS */
    .status-badge {
        display: inline-block;
        background: white;
        color: black;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: bold;
        margin-top: 8px;
    }

    /* RIGHT SIDE INFORMATION */
    .ticket-info {
        margin-top: 30px;
        font-size: 1.1rem;
        line-height: 2;
    }

    .ticket-info p {
        margin: 5px 0;
    }

    .ticket-label {
        color: #94a3b8;
    }

    .ticket-value {
        color: white;
    }

    /* MOBILE */
    @media (max-width: 700px) {

        .dashboard-container {
            flex-direction: column;
            margin: 10px;
        }

        .sidebar-left {
            height: auto;
            max-height: 400px;
        }

        .content-right {
            padding: 25px 20px;
        }

    }
</style>


<div class="dashboard-container">


    <!-- ========================================================= -->
    <!-- LEFT SIDE: QUEUE HISTORY -->
    <!-- ========================================================= -->

    <div class="sidebar-left">

        <h3 style="margin-top: 0; margin-bottom: 20px;">
            Queue History
        </h3>


        @foreach($tickets as $ticket)

            <a
                href="{{ url('/history?ticket=' . $ticket->id) }}"
                class="ticket-link"
            >

                <div
                    class="ticket-card
                    {{ request('ticket') == $ticket->id ? 'active' : '' }}"
                >

                    <h4
                        style="
                            margin: 0;
                            font-size: 1.1rem;
                        "
                    >
                        {{ $ticket->tracking_number ?? 'No Tracking #' }}
                    </h4>


                    <span
                        style="
                            font-size: 0.8rem;
                            color: #cbd5e1;
                        "
                    >
                        {{ $ticket->created_at
                            ? $ticket->created_at->format('M d, Y h:i A')
                            : 'No date'
                        }}
                    </span>


                    <br>


                    <span class="status-badge">
                        {{ strtoupper($ticket->status ?? 'UNKNOWN') }}
                    </span>

                </div>

            </a>

        @endforeach


        @if($tickets->isEmpty())

            <div style="color: #94a3b8; text-align: center;">

                No queue history found.

            </div>

        @endif

    </div>



    <!-- ========================================================= -->
    <!-- RIGHT SIDE: TICKET DETAILS -->
    <!-- ========================================================= -->

    <div class="content-right">


        @if($selectedTicket)


            <!-- TRACKING NUMBER -->

            <h2
                style="
                    margin-top: 0;
                    color: #60a5fa;
                "
            >

                Tracking Number:

                {{ $selectedTicket->tracking_number ?? 'N/A' }}

            </h2>



            <div class="ticket-info">


                <!-- ================================================= -->
                <!-- NAME -->
                <!-- ================================================= -->

                <p>

                    <strong class="ticket-label">
                        Name:
                    </strong>

                    <span class="ticket-value">

                        @php

                            $student = null;

                            if ($selectedTicket->student_id) {

                                $student =
                                    \App\Models\Student::find(
                                        $selectedTicket->student_id
                                    );

                            }

                        @endphp


                        @if($student)

                            {{ $student->name
                                ?? $student->full_name
                                ?? $student->student_name
                                ?? 'N/A'
                            }}

                        @else

                            {{ $selectedTicket->name ?? 'N/A' }}

                        @endif

                    </span>

                </p>



                <!-- ================================================= -->
                <!-- SCHOOL USN -->
                <!-- ================================================= -->

                <p>

                    <strong class="ticket-label">
                        School USN:
                    </strong>

                    <span class="ticket-value">

                        @if($student)

                            {{ $student->student_number ?? 'N/A' }}

                        @else

                            N/A

                        @endif

                    </span>

                </p>



                <!-- ================================================= -->
                <!-- PHONE NUMBER -->
                <!-- ================================================= -->

                <p>

                    <strong class="ticket-label">
                        Phone Number:
                    </strong>

                    <span class="ticket-value">

                        {{ $selectedTicket->mobile_number ?? 'N/A' }}

                    </span>

                </p>



                <!-- ================================================= -->
                <!-- PURPOSE -->
                <!-- ================================================= -->

                <p>

                    <strong class="ticket-label">
                        Purpose:
                    </strong>

                    <span class="ticket-value">

                        @if($selectedTicket->transaction_request_id)

                            @php

                                $transactionRequest =
                                    \App\Models\TransactionRequest::find(
                                        $selectedTicket->transaction_request_id
                                    );

                            @endphp


                            {{ $transactionRequest->description ?? 'N/A' }}

                        @else

                            {{ $selectedTicket->purpose ?? 'N/A' }}

                        @endif

                    </span>

                </p>



                <!-- ================================================= -->
                <!-- STATUS -->
                <!-- ================================================= -->

                <p>

                    <strong class="ticket-label">
                        Status:
                    </strong>

                    <span class="ticket-value">

                        {{ $selectedTicket->status
                            ? ucwords(
                                str_replace(
                                    '_',
                                    ' ',
                                    $selectedTicket->status
                                )
                            )
                            : 'N/A'
                        }}

                    </span>

                </p>



                <!-- ================================================= -->
                <!-- ASSIGNED WINDOW -->
                <!-- ================================================= -->

                <p>

                    <strong class="ticket-label">
                        Assigned Window:
                    </strong>

                    <span class="ticket-value">

                        {{ $selectedTicket->assigned_teller
                            ?? 'Not assigned'
                        }}

                    </span>

                </p>



                <!-- ================================================= -->
                <!-- QUEUE DATE -->
                <!-- ================================================= -->

                <p>

                    <strong class="ticket-label">
                        Queue Date:
                    </strong>

                    <span class="ticket-value">

                        {{ $selectedTicket->queue_date
                            ? \Carbon\Carbon::parse(
                                $selectedTicket->queue_date
                            )->format('F d, Y')
                            : 'N/A'
                        }}

                    </span>

                </p>



                <!-- ================================================= -->
                <!-- DATE GENERATED -->
                <!-- ================================================= -->

                <p>

                    <strong class="ticket-label">
                        Date Generated:
                    </strong>

                    <span class="ticket-value">

                        {{ $selectedTicket->created_at
                            ? $selectedTicket->created_at->format(
                                'F d, Y - h:i A'
                            )
                            : 'N/A'
                        }}

                    </span>

                </p>


            </div>


        @else


            <!-- ===================================================== -->
            <!-- NO TICKET SELECTED -->
            <!-- ===================================================== -->

            <div
                style="
                    text-align: center;
                    margin-top: 100px;
                    color: #94a3b8;
                "
            >

                <h2>
                    No Ticket Selected
                </h2>

                <p>
                    Select a tracking number from the left
                    to view details.
                </p>

            </div>


        @endif


    </div>

</div>

@endsection
