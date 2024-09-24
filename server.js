// Route to update group information
app.post('/update-group', (req, res) => {
    const { groupId, groupName } = req.body;

    // SQL query to update the group in the database
    const updateGroupQuery = 'UPDATE groups SET name = ? WHERE id = ?';

    // Execute the query
    db.query(updateGroupQuery, [groupName, groupId], (err, result) => {
        if (err) {
            console.error('Error updating group:', err);
            return res.status(500).json({ message: 'Error updating group' });
        }

        // Send success response
        res.json({ message: 'Group updated successfully' });
    });
});
