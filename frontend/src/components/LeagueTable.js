import React, { useState, useEffect } from 'react';
import axios from 'axios';

const LeagueTable = () => {
    const [table, setTable] = useState([]);
    const [predictions, setPredictions] = useState([]); // Состояние для предсказаний
    const [results, setResults] = useState([]);
    const [isFinished, setIsFinished] = useState(false);
    const [champion, setChampion] = useState(null);

    useEffect(() => {
        fetchTable();
    }, []);

    const fetchTable = () => {
        axios.get('http://localhost:3030/api/table')
            .then(response => {
                setTable(response.data.leagueTable || []);
                setPredictions(response.data.predictions || []); // Устанавливаем предсказания
                setIsFinished(response.data.isFinished);
                setChampion(response.data.champion);
            })
            .catch(error => console.error('Error fetching league table:', error));
    };

    const playNextRound = () => {
        axios.get('http://localhost:3030/api/play')
            .then(response => {
                setTable(response.data.leagueTable || []);
                setResults(response.data.matchResults || []);
                setPredictions(response.data.predictions || []); // Устанавливаем предсказания
                setIsFinished(response.data.isFinished);
                setChampion(response.data.champion);
            })
            .catch(error => console.error('Error playing next round:', error));
    };

    const resetLeague = () => {
        axios.post('http://localhost:3030/api/reset')
            .then(response => {
                alert(response.data.message);
                fetchTable();
            })
            .catch(error => console.error('Error resetting league:', error));
    };

    return (
        <div>
            <h1>League Table</h1>
            <table>
                <thead>
                <tr>
                    <th>Team</th>
                    <th>PTS</th>
                    <th>P</th>
                    <th>W</th>
                    <th>D</th>
                    <th>L</th>
                    <th>GD</th>
                </tr>
                </thead>
                <tbody>
                {table.map((team, index) => (
                    <tr key={index}>
                        <td>{team.name}</td>
                        <td>{team.points}</td>
                        <td>{team.played}</td>
                        <td>{team.wins}</td>
                        <td>{team.draws}</td>
                        <td>{team.losses}</td>
                        <td>{team.goalDifference}</td>
                    </tr>
                ))}
                </tbody>
            </table>

            <h2>Championship Predictions</h2>
            <ul>
                {predictions.length > 0 ? (
                    predictions.map((prediction, index) => (
                        <li key={index}>{prediction.team}: {prediction.prediction}</li>
                    ))
                ) : (
                    <li>No predictions available</li>
                )}
            </ul>

            {isFinished && champion ? (
                <div>
                    <h2>Champion: {champion}</h2>
                </div>
            ) : (
                <button onClick={playNextRound}>Play Next Round</button>
            )}

            <h2>Match Results</h2>
            <ul>
                {results.map((result, index) => (
                    <li key={index}>{result.team1} {result.score} {result.team2}</li>
                ))}
            </ul>

            <button onClick={resetLeague}>Reset League</button>
        </div>
    );
};

export default LeagueTable;
