import React, { useState } from 'react';
import EmployeeList from './components/EmployeeList';
import AddEmployee from './components/AddEmployee';
import UpdateEmployee from './components/UpdateEmployee';
import './App.css'; 

function App() {
  const [editingEmployee, setEditingEmployee] = useState(null); 
  const [refresh, setRefresh] = useState(false); 

  const handleRefresh = () => {
    setRefresh(!refresh); 
  };

  
  const handleCancelEdit = () => {
    setEditingEmployee(null);
  };

  return (
    <div className="app-container">
      <h1 className="main-title">Employee Management System</h1>

      
      <EmployeeList
        onEdit={(employee) => setEditingEmployee(employee)} 
        onDelete={handleRefresh}                           
        key={refresh}                                      
      />

    
      {editingEmployee ? (
        <UpdateEmployee
          employee={editingEmployee}
          onUpdate={() => {
            setEditingEmployee(null); 
            handleRefresh();          
          }}
          onCancel={handleCancelEdit} 
        />
      ) : (
        <AddEmployee onAdd={handleRefresh} /> 
      )}
    </div>
  );
}

export default App;
