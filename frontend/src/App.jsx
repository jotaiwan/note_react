import React, { useState } from "react";
import Menu from "./components/Menu";
import Notes from "./components/Notes";

function App() {
  const [notesReloadTrigger, setNotesReloadTrigger] = useState(0);
  const [showNotes, setShowNotes] = useState(true);  // 控制 Notes 顯示的狀態

  // 调 API 新增笔记
  // 通知 Notes：你该重新拉数据了
  // 真正的 handleSaveNote 函式本體，在 App.jsx
  const handleSaveNote = async (data) => {
    // await createNote(data);    // don't do this, because it will post 2nd time 
    setNotesReloadTrigger((prev) => prev + 1); // 觸發 Notes 重新載入
  };

  return (
    <div className="p-2 menu-container">
      {/* 👉 把「這個函式的參考」傳給 Menu */}
      <Menu onSaveNote={handleSaveNote} />

      {/* 控制 Notes 顯示的條件 */}
      <Notes reloadTrigger={notesReloadTrigger} />
    </div>
  );
}

export default App;
