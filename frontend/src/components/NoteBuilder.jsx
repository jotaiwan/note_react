// // frontend/src/components/NoteBuilder.jsx
// import React, { useEffect } from 'react';
// import { initNoteBuilder } from '../assets/note/noteBuilder';  // 引入初始化函數

// const NoteBuilder = () => {
//     useEffect(() => {
//         // 初始化筆記建立功能
//         initNoteBuilder();
//     }, []);  // 只在組件掛載時執行一次

//     return (
//         <div>
//             <h1>Note Builder</h1>
//             <textarea id="raw" placeholder="Write your note here..."></textarea>
//             <div id="preview"></div>
//             <select id="emojiSelect">
//                 <option value="🙂">🙂</option>
//                 <option value="❤️">❤️</option>
//                 {/* 其他 emoji 選項 */}
//             </select>
//         </div>
//     );
// };

// export default NoteBuilder;
