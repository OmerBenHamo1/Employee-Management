import React, { useEffect, useState } from 'react';
import { getEmployees } from '../services/api';
import DeleteEmployee from './DeleteEmployee';
import './EmployeeList.css'; 

function EmployeeList({ onEdit, onDelete }) {
  const [employees, setEmployees] = useState([]);

  useEffect(() => {
    fetchEmployees();
    
  }, [onDelete]); 

  const fetchEmployees = async () => {
    try {
      const data = await getEmployees();
      setEmployees(data);
    } catch (error) {
      console.error("Failed to fetch employees:", error);
    }
  };

  return (
    <div className="employee-list-container">
      <h2 className="employee-list-title">Employee List</h2>
      <table className="employee-list-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Position</th>
            <th>Salary</th>
            <th>Hire Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {employees.length > 0 ? (
            employees.map((emp) => (
              <tr key={emp.id}>
                <td>{`${emp.first_name} ${emp.last_name}`}</td>
                <td>{emp.position}</td>
                <td>{emp.salary}</td>
                <td>{emp.hire_date}</td>
                <td className="actions-container">
                  <button
                    className="btn-update"
                    onClick={() => onEdit(emp)}
                  >
                    Update
                  </button>
                  <DeleteEmployee
                    employeeId={emp.id}
                    onDelete={fetchEmployees}
                  />
                </td>
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan="5" style={{ textAlign: 'center' }}>
                No employees found.
              </td>
            </tr>
          )}
        </tbody>
      </table>
    </div>
  );
}

export default EmployeeList;
