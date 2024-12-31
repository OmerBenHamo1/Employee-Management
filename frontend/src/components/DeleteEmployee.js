import React, { useState } from 'react';
import { deleteEmployee } from '../services/api';
import 'react-confirm-alert/src/react-confirm-alert.css'; 
import { confirmAlert } from 'react-confirm-alert'; 
import './DeleteEmployee.css';

function DeleteEmployee({ employeeId, onDelete }) {
  const [errorMessage, setErrorMessage] = useState('');

  const showSuccessPopup = (message) => {
    confirmAlert({
      title: 'Success',
      message: message,
      buttons: [
        {
          label: 'OK',
          onClick: () => {}, 
        },
      ],
    });
  };

  const handleDelete = async () => {
    confirmAlert({
      title: 'Confirm Deletion',
      message: `Are you sure you want to delete the employee with ID: ${employeeId}?`,
      buttons: [
        {
          label: 'Yes',
          onClick: async () => {
            try {
              await deleteEmployee(employeeId);
              showSuccessPopup(`Employee with ID: ${employeeId} deleted successfully!`);
              setErrorMessage('');
              onDelete(); 
            } catch (error) {
              console.error('Failed to delete employee:', error);
              setErrorMessage('Failed to delete employee. Please try again.');
              setTimeout(() => {
                setErrorMessage('');
              }, 2000);
            }
          },
        },
        {
          label: 'No',
          onClick: () => {}, 
        },
      ],
    });
  };

  return (
    <div className="delete-employee-container">
      <div className="message-container">
        {errorMessage && <div className="error-message">{errorMessage}</div>}
      </div>

      <button onClick={handleDelete} className="btn-delete">
        Delete
      </button>
    </div>
  );
}

export default DeleteEmployee;
