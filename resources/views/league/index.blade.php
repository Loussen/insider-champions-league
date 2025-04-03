<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premier League Simulation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Premier League Simulation</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">League Table</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left">Team</th>
                                <th class="px-4 py-2 text-center">P</th>
                                <th class="px-4 py-2 text-center">W</th>
                                <th class="px-4 py-2 text-center">D</th>
                                <th class="px-4 py-2 text-center">L</th>
                                <th class="px-4 py-2 text-center">GF</th>
                                <th class="px-4 py-2 text-center">GA</th>
                                <th class="px-4 py-2 text-center">GD</th>
                                <th class="px-4 py-2 text-center">Pts</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($table as $row)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $row->team->name }}</td>
                                <td class="px-4 py-2 text-center">{{ $row->played }}</td>
                                <td class="px-4 py-2 text-center">{{ $row->won }}</td>
                                <td class="px-4 py-2 text-center">{{ $row->drawn }}</td>
                                <td class="px-4 py-2 text-center">{{ $row->lost }}</td>
                                <td class="px-4 py-2 text-center">{{ $row->goals_for }}</td>
                                <td class="px-4 py-2 text-center">{{ $row->goals_against }}</td>
                                <td class="px-4 py-2 text-center">{{ $row->goal_difference }}</td>
                                <td class="px-4 py-2 text-center font-bold">{{ $row->points }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Championship Predictions</h2>
                <div class="space-y-4">
                    @foreach($predictions as $team => $chance)
                    <div class="flex justify-between items-center">
                        <span>{{ $team }}</span>
                        <span class="font-bold">{{ $chance }}%</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-8 space-x-4">
            <form action="{{ route('league.fixtures.generate') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Generate Fixtures
                </button>
            </form>

            <form action="{{ route('league.play.week') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Play Next Week
                </button>
            </form>

            <form action="{{ route('league.play.all') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
                    Play All Matches
                </button>
            </form>
        </div>

        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-4">Match Results</h2>
            <div class="space-y-6">
                @foreach($matches as $week => $weekMatches)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Week {{ $week }}</h3>
                    <div class="space-y-4">
                        @foreach($weekMatches as $match)
                        <div class="flex items-center justify-between">
                            <span class="w-1/3 text-right">{{ $match->homeTeam->name }}</span>
                            @if($match->played)
                                <span class="w-1/3 text-center font-bold">
                                    {{ $match->home_team_score }} - {{ $match->away_team_score }}
                                </span>
                            @else
                                <form action="{{ route('league.match.update', $match) }}" method="POST" class="w-1/3 flex justify-center items-center space-x-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="home_score" class="w-16 text-center border rounded" min="0" required>
                                    <span>-</span>
                                    <input type="number" name="away_score" class="w-16 text-center border rounded" min="0" required>
                                    <button type="submit" class="bg-gray-500 text-white px-2 py-1 rounded text-sm">Save</button>
                                </form>
                            @endif
                            <span class="w-1/3">{{ $match->awayTeam->name }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>
