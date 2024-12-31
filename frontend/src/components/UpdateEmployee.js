import React, { useState } from 'react';
import { updateEmployee } from '../services/api';
import 'react-confirm-alert/src/react-confirm-alert.css'; 
import { confirmAlert } from 'react-confirm-alert'; 
import './UpdateEmployee.css';

function UpdateEmployee({ employee, onUpdate, onCancel }) {
  const [updatedEmployee, setUpdatedEmployee] = useState(employee);
  const [errors, setErrors] = useState({});

  const handleChange = (e) => {
    setUpdatedEmployee({ ...updatedEmployee, [e.target.name]: e.target.value });
  };

  const validate = () => {
    const newErrors = {};
    if (!/^[a-zA-Z\s]+$/.test(updatedEmployee.first_name.trim())) {
      newErrors.first_name = 'First name must contain only letters and spaces.';
    }
    if (!/^[a-zA-Z\s]+$/.test(updatedEmployee.last_name.trim())) {
      newErrors.last_name = 'Last name must contain only letters and spaces.';
    }
    if (!/^\S+@\S+\.\S+$/.test(updatedEmployee.email.trim())) {
      newErrors.email = 'Invalid email address.';
    }
    if (!updatedEmployee.position.trim()) {
      newErrors.position = 'Position is required.';
    }
    if (!/^\d{4}-\d{2}-\d{2}$/.test(updatedEmployee.hire_date.trim())) {
      newErrors.hire_date = 'Hire date must be in YYYY-MM-DD format.';
    }
    if (!/^\d+(\.\d{1,2})?$/.test(updatedEmployee.salary.trim())) {
      newErrors.salary = 'Salary must be a valid number.';
    }
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!validate()) return;

    try {
      console.log("Updating employee...");
      await updateEmployee(updatedEmployee.id, updatedEmployee);

      confirmAlert({
        title: 'Success',
        message: 'Employee details updated successfully!',
        buttons: [
          {
            label: 'OK',
            onClick: () => onUpdate(), 
          },
        ],
      });
    } catch (error) {
      confirmAlert({
        title: 'Error',
        message: 'Failed to update employee. Please try again.',
        buttons: [
          {
            label: 'OK',
            onClick: () => {}, 
          },
        ],
      });
    }
  };

  const handleBack = () => {
    setUpdatedEmployee({
      first_name: '',
      last_name: '',
      email: '',
      position: '',
      hire_date: '',
      salary: '',
    });
    setErrors({});
    onCancel();
  };

  return (
    <div className="update-employee-container">
      <h2 className="update-title">Update Employee</h2>

      <form onSubmit={handleSubmit} className="update-form" noValidate>
        <div className="update-form-group">
          <label className="update-form-label">First Name</label>
          <input
            type="text"
            name="first_name"
            className="update-form-input"
            placeholder="Enter first name"
            value={updatedEmployee.first_name}
            onChange={handleChange}
            required
          />
          {errors.first_name && (
            <small className="update-error-msg">{errors.first_name}</small>
          )}
        </div>

        <div className="update-form-group">
          <label className="update-form-label">Last Name</label>
          <input
            type="text"
            name="last_name"
            className="update-form-input"
            placeholder="Enter last name"
            value={updatedEmployee.last_name}
            onChange={handleChange}
            required
          />
          {errors.last_name && (
            <small className="update-error-msg">{errors.last_name}</small>
          )}
        </div>

        <div className="update-form-group">
          <label className="update-form-label">Email</label>
          <input
            type="email"
            name="email"
            className="update-form-input"
            placeholder="Enter email"
            value={updatedEmployee.email}
            onChange={handleChange}
            required
          />
          {errors.email && (
            <small className="update-error-msg">{errors.email}</small>
          )}
        </div>

        <div className="update-form-group">
          <label className="update-form-label">Position</label>
          <input
            type="text"
            name="position"
            className="update-form-input"
            placeholder="Enter position"
            value={updatedEmployee.position}
            onChange={handleChange}
            required
          />
          {errors.position && (
            <small className="update-error-msg">{errors.position}</small>
          )}
        </div>

        <div className="update-form-group">
          <label className="update-form-label">Hire Date</label>
          <input
            type="date"
            name="hire_date"
            className="update-form-input"
            value={updatedEmployee.hire_date}
            onChange={handleChange}
            required
          />
          {errors.hire_date && (
            <small className="update-error-msg">{errors.hire_date}</small>
          )}
        </div>

        <div className="update-form-group">
          <label className="update-form-label">Salary</label>
          <input
            type="text"
            name="salary"
            className="update-form-input"
            placeholder="Enter salary"
            value={updatedEmployee.salary}
            onChange={handleChange}
            required
          />
          {errors.salary && (
            <small className="update-error-msg">{errors.salary}</small>
          )}
        </div>

        <div className="update-buttons">
          <button type="button" className="btn-back-update" onClick={handleBack}>
            Back
          </button>
          <button type="submit" className="btn-submit-update">
            Update Employee
          </button>
        </div>
      </form>
    </div>
  );
}

export default UpdateEmployee;
