// import React, { useState, useEffect } from 'react';
// import ReactDOM from 'react-dom/client'; // use React 18 style

// function App() {
//     const [note, setNote] = useState(null);    // store API data
//     const [loading, setLoading] = useState(true);
//     const [error, setError] = useState(null);

//     useEffect(() => {
//         // API request
//         fetch('http://localhost:8000/api/notes/5')
//             .then((res) => {
//                 if (!res.ok) throw new Error('Network response was not ok');
//                 return res.json();
//             })
//             .then((data) => {
//                 setNote(data);   // store data
//                 setLoading(false);
//             })
//             .catch((err) => {
//                 setError(err.message);
//                 setLoading(false);
//             });
//     }, []);

//     if (loading) return <div>Loading...</div>;
//     if (error) return <div>Error: {error}</div>;

//     return (
//         <div>
//             <h1>Note Detail</h1>
//             <p><strong>ID:</strong> {note.id}</p>
//             <p><strong>Title:</strong> {note.title}</p>
//             <p><strong>Content:</strong> {note.content}</p>
//         </div>
//     );
// }

// // React 18 render
// const root = ReactDOM.createRoot(document.getElementById('root'));
// root.render(<App />);
