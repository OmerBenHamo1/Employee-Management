import React, { useState } from 'react';
import 'react-confirm-alert/src/react-confirm-alert.css'; 
import { confirmAlert } from 'react-confirm-alert'; 
import './AddEmployee.css';

function AddEmployee({ onAdd }) {
  const [showForm, setShowForm] = useState(false);
  const [newEmployee, setNewEmployee] = useState({
    first_name: '',
    last_name: '',
    email: '',
    position: '',
    hire_date: '',
    salary: '',
  });
  const [errors, setErrors] = useState({});

  const handleChange = (e) => {
    setNewEmployee({ ...newEmployee, [e.target.name]: e.target.value });
  };

  const validate = () => {
    const newErrors = {};
    if (!/^[a-zA-Z\s]+$/.test(newEmployee.first_name.trim())) {
      newErrors.first_name = 'First name must contain only letters and spaces.';
    }
    if (!/^[a-zA-Z\s]+$/.test(newEmployee.last_name.trim())) {
      newErrors.last_name = 'Last name must contain only letters and spaces.';
    }
    if (!/^\S+@\S+\.\S+$/.test(newEmployee.email.trim())) {
      newErrors.email = 'Invalid email address.';
    }
    if (!newEmployee.position.trim()) {
      newErrors.position = 'Position is required.';
    }
    if (!/^\d{4}-\d{2}-\d{2}$/.test(newEmployee.hire_date.trim())) {
      newErrors.hire_date = 'Hire date must be in YYYY-MM-DD format.';
    }
    if (!/^\d+(\.\d{1,2})?$/.test(newEmployee.salary.trim())) {
      newErrors.salary = 'Salary must be a valid number.';
    }
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!validate()) return;

    try {
      const response = await fetch(
        'http://localhost/checkPoint/backend/api/api.php/employees',
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(newEmployee),
        }
      );

      const data = await response.json();

      if (response.ok) {
        confirmAlert({
          title: 'Success',
          message: 'Employee added successfully!',
          buttons: [
            {
              label: 'OK',
              onClick: () => {
                onAdd(); 
                setShowForm(false); 
              },
            },
          ],
        });
      } else {
        confirmAlert({
          title: 'Error',
          message: data.error || 'Failed to add employee.',
          buttons: [
            {
              label: 'OK',
              onClick: () => {}, 
            },
          ],
        });
      }
    } catch (error) {
      confirmAlert({
        title: 'Error',
        message: 'Failed to add employee. Please try again.',
        buttons: [
          {
            label: 'OK',
            onClick: () => {}, 
          },
        ],
      });
    }
  };

  const handleShowForm = () => {
    setShowForm(true);
  };

  const handleBack = () => {
    setShowForm(false);
    setNewEmployee({
      first_name: '',
      last_name: '',
      email: '',
      position: '',
      hire_date: '',
      salary: '',
    });
    setErrors({});
  };

  return (
    <div className="add-employee-container">
      {!showForm ? (
        <button
          className="btn-submit-add"
          onClick={handleShowForm}
        >
          ADD EMPLOYEE
        </button>
      ) : (
        <>
          <h2 className="add-title">Add Employee</h2>

          <form onSubmit={handleSubmit} className="add-form" noValidate>
            <div className="add-form-group">
              <label className="add-form-label">First Name</label>
              <input
                type="text"
                name="first_name"
                className="add-form-input"
                placeholder="Enter first name"
                value={newEmployee.first_name}
                onChange={handleChange}
                required
              />
              {errors.first_name && (
                <small className="add-error-msg">{errors.first_name}</small>
              )}
            </div>

            <div className="add-form-group">
              <label className="add-form-label">Last Name</label>
              <input
                type="text"
                name="last_name"
                className="add-form-input"
                placeholder="Enter last name"
                value={newEmployee.last_name}
                onChange={handleChange}
                required
              />
              {errors.last_name && (
                <small className="add-error-msg">{errors.last_name}</small>
              )}
            </div>

            <div className="add-form-group">
              <label className="add-form-label">Email</label>
              <input
                type="email"
                name="email"
                className="add-form-input"
                placeholder="Enter email"
                value={newEmployee.email}
                onChange={handleChange}
                required
              />
              {errors.email && (
                <small className="add-error-msg">{errors.email}</small>
              )}
            </div>

            <div className="add-form-group">
              <label className="add-form-label">Position</label>
              <input
                type="text"
                name="position"
                className="add-form-input"
                placeholder="Enter position"
                value={newEmployee.position}
                onChange={handleChange}
                required
              />
              {errors.position && (
                <small className="add-error-msg">{errors.position}</small>
              )}
            </div>

            <div className="add-form-group">
              <label className="add-form-label">Hire Date</label>
              <input
                type="date"
                name="hire_date"
                className="add-form-input"
                value={newEmployee.hire_date}
                onChange={handleChange}
                required
              />
              {errors.hire_date && (
                <small className="add-error-msg">{errors.hire_date}</small>
              )}
            </div>

            <div className="add-form-group">
              <label className="add-form-label">Salary</label>
              <input
                type="text"
                name="salary"
                className="add-form-input"
                placeholder="Enter salary"
                value={newEmployee.salary}
                onChange={handleChange}
                required
              />
              {errors.salary && (
                <small className="add-error-msg">{errors.salary}</small>
              )}
            </div>

            <div className="add-buttons">
              <button
                type="button"
                className="btn-back-add"
                onClick={handleBack}
              >
                Back
              </button>
              <button type="submit" className="btn-submit-add">
                Add Employee
              </button>
            </div>
          </form>
        </>
      )}
    </div>
  );
}

export default AddEmployee;
