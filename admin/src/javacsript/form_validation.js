document.getElementById('eventForm').addEventListener('submit', function(e) {
    const startDate = new Date(document.querySelector('input[name="event_start_date"]').value);
    const endDate = new Date(document.querySelector('input[name="event_end_date"]').value);
    
    if (endDate < startDate) {
        e.preventDefault();
        M.toast({html: 'End date cannot be before start date!'});
    }
});