# JodTod - Settlement Calculation Logic

This is the CORE algorithm of JodTod. Every developer working on this must understand this completely.

---

## Overview

When the "Settle Up" button is clicked in a group, the system:
1. Calculates how much each member paid
2. Calculates how much each member's share was
3. Finds net balance (paid - share)
4. Optimizes to find minimum number of transactions
5. Shows "who pays whom how much"

---

## Step-by-Step Algorithm

### STEP 1: Fetch Unsettled Expenses

```sql
SELECT e.*, es.user_id as split_user_id, es.share_amount
FROM expenses e
JOIN expense_splits es ON e.id = es.expense_id
WHERE e.group_id = :group_id
AND e.is_settled = false
AND e.deleted_at IS NULL
```

---

### STEP 2: Calculate Total Paid by Each Member

Loop through all unsettled expenses and sum up how much each member paid.

```
total_paid = []
for each expense:
    total_paid[expense.paid_by] += expense.amount
```

Example:
```
Amit  paid: 3000 (Hotel) + 600 (Shopping) = 3600
Rahul paid: 1500 (Khana) = 1500
Priya paid: 900 (Cab) = 900
```

---

### STEP 3: Calculate Total Share of Each Member

Loop through expense_splits table and sum up each member's share.

```
total_share = []
for each expense_split:
    total_share[split.user_id] += split.share_amount
```

Example:
```
Amit  share: 1000 + 500 + 300 + 200 = 2000
Rahul share: 1000 + 500 + 300 + 200 = 2000
Priya share: 1000 + 500 + 300 + 200 = 2000
```

---

### STEP 4: Calculate Net Balance

```
net_balance = []
for each member:
    net_balance[member] = total_paid[member] - total_share[member]
```

```
Amit  : 3600 - 2000 = +1600  (creditor - paisa milega)
Rahul : 1500 - 2000 = -500   (debtor - paisa dena hai)
Priya :  900 - 2000 = -1100  (debtor - paisa dena hai)
```

**Validation:** Sum of all net_balances MUST be 0. If not, there is a calculation error.

---

### STEP 5: Optimize Transactions (Minimum Transfers Algorithm)

This is the greedy approach - simple and effective for most cases.

```
Algorithm: Greedy Min Transfers

1. Separate members into two lists:
   - creditors[] = members with positive balance (sorted descending)
   - debtors[] = members with negative balance (sorted ascending by absolute value)

2. While both lists are not empty:
   a. Take the largest creditor and largest debtor
   b. Transfer amount = min(creditor.balance, abs(debtor.balance))
   c. Create transaction: debtor -> creditor = transfer amount
   d. Update balances:
      - creditor.balance -= transfer amount
      - debtor.balance += transfer amount
   e. If creditor.balance == 0, remove from creditors list
   f. If debtor.balance == 0, remove from debtors list
```

**Walkthrough with our example:**

```
Creditors: [Amit: +1600]
Debtors:   [Priya: -1100, Rahul: -500]

Round 1:
  Largest debtor: Priya (-1100)
  Largest creditor: Amit (+1600)
  Transfer = min(1600, 1100) = 1100
  Transaction: Priya -> Amit = Rs.1100
  Updated: Amit = +500, Priya = 0 (removed)

Round 2:
  Largest debtor: Rahul (-500)
  Largest creditor: Amit (+500)
  Transfer = min(500, 500) = 500
  Transaction: Rahul -> Amit = Rs.500
  Updated: Amit = 0 (removed), Rahul = 0 (removed)

Both lists empty -> DONE!

Result:
  1. Priya -> Amit : Rs.1100
  2. Rahul -> Amit : Rs.500
  Total transactions: 2
```

---

### STEP 6: Store Settlements in Database

```sql
INSERT INTO settlements (group_id, from_user, to_user, amount, status, created_at)
VALUES
  (:group_id, :priya_id, :amit_id, 1100, 'pending', NOW()),
  (:group_id, :rahul_id, :amit_id, 500, 'pending', NOW());
```

Note: Status is 'pending' initially. It becomes 'completed' when the user confirms payment.

---

### STEP 7: When User Marks Settlement as Completed

```sql
-- Mark settlement as completed
UPDATE settlements
SET status = 'completed', settled_at = NOW()
WHERE id = :settlement_id;

-- Check if ALL settlements for this group are completed
-- If yes, mark all related expenses as settled
UPDATE expenses
SET is_settled = true
WHERE group_id = :group_id
AND is_settled = false;

UPDATE expense_splits
SET is_settled = true
WHERE expense_id IN (
    SELECT id FROM expenses
    WHERE group_id = :group_id
);
```

---

## Complex Example (5 Members)

```
Group: "Office Lunch Group"
Members: A, B, C, D, E

Expenses:
  1. Rs.1000 - A paid - Equal split (200 each)
  2. Rs.500  - B paid - Equal split (100 each)
  3. Rs.2000 - C paid - Custom split: A=500, B=300, C=400, D=500, E=300
  4. Rs.1500 - A paid - Equal split (300 each)

STEP 2 - Total Paid:
  A = 1000 + 1500 = 2500
  B = 500
  C = 2000
  D = 0
  E = 0

STEP 3 - Total Share:
  A = 200 + 100 + 500 + 300 = 1100
  B = 200 + 100 + 300 + 300 = 900
  C = 200 + 100 + 400 + 300 = 1000
  D = 200 + 100 + 500 + 300 = 1100
  E = 200 + 100 + 300 + 300 = 900
  Check: 1100+900+1000+1100+900 = 5000 = 1000+500+2000+1500 (correct!)

STEP 4 - Net Balance:
  A = 2500 - 1100 = +1400  (milega)
  B = 500 - 900   = -400   (dega)
  C = 2000 - 1000 = +1000  (milega)
  D = 0 - 1100    = -1100  (dega)
  E = 0 - 900     = -900   (dega)
  Check: +1400+1000-400-1100-900 = 0 (correct!)

STEP 5 - Optimize:
  Creditors: [A: +1400, C: +1000]
  Debtors:   [D: -1100, E: -900, B: -400]

  Round 1: D(-1100) -> A(+1400) = 1100
    A = +300, D = 0 (done)

  Round 2: E(-900) -> A(+300)... wait, E owes 900 but A only needs 300
    E(-900) -> A(+300) = 300
    A = 0 (done), E = -600

  Round 3: E(-600) -> C(+1000) = 600
    C = +400, E = 0 (done)

  Round 4: B(-400) -> C(+400) = 400
    C = 0 (done), B = 0 (done)

  Result:
    1. D -> A : Rs.1100
    2. E -> A : Rs.300
    3. E -> C : Rs.600
    4. B -> C : Rs.400
    Total transactions: 4 (instead of potentially many more)
```

---

## Edge Cases to Handle

### 1. Single expense in group
- Simple: just split the amount, no complex optimization needed

### 2. Only one person paid everything
- Everyone else pays that person their share

### 3. Everyone paid equal
- Net balance = 0 for everyone, no settlement needed
- Show message: "Sab barabar hai, koi settlement nahi!"

### 4. Two people only
- Maximum 1 transaction
- Simple subtraction

### 5. Rounding issues (equal split)
- Rs.100 split among 3 = 33.33, 33.33, 33.34
- Extra paisa goes to the last person OR the person who paid
- Always use DECIMAL(12,2) in database, never FLOAT

### 6. Member leaves or is removed mid-trip with unsettled expenses
- Member is **deactivated** (is_active = false) instead of removed from the group
- Deactivated members are **excluded from new expense splits** (won't appear in Add Expense form)
- Deactivated members **remain in settlement calculations** for their existing unsettled expenses
- They still receive settlement notifications and can mark payments as done
- Admin can **reactivate** a deactivated member if they return
- Member can only be **fully removed** (deleted from group) when they have no unsettled expenses
- This prevents any data integrity issues while allowing flexible group membership during trips

### 7. Expense deleted after some settlements
- Recalculate balances
- If settlement already completed, don't reverse it
- Show warning before deleting

### 8. No unsettled expenses
- Show message: "Koi unsettled expense nahi hai!"
- Disable settlement button

---

## PHP Implementation Pseudocode

```php
public function calculateSettlement(Group $group)
{
    // Step 1: Fetch unsettled expenses
    $expenses = Expense::where('group_id', $group->id)
        ->where('is_settled', false)
        ->with('splits')
        ->get();

    if ($expenses->isEmpty()) {
        return ['message' => 'Koi unsettled expense nahi hai!'];
    }

    // Step 2: Calculate total paid
    $totalPaid = [];
    foreach ($expenses as $expense) {
        $totalPaid[$expense->paid_by] = ($totalPaid[$expense->paid_by] ?? 0) + $expense->amount;
    }

    // Step 3: Calculate total share
    $totalShare = [];
    foreach ($expenses as $expense) {
        foreach ($expense->splits as $split) {
            $totalShare[$split->user_id] = ($totalShare[$split->user_id] ?? 0) + $split->share_amount;
        }
    }

    // Step 4: Calculate net balances
    $members = $group->members->pluck('id')->toArray();
    $balances = [];
    foreach ($members as $memberId) {
        $paid = $totalPaid[$memberId] ?? 0;
        $share = $totalShare[$memberId] ?? 0;
        $balances[$memberId] = $paid - $share;
    }

    // Validate: sum should be 0
    $sum = array_sum($balances);
    if (abs($sum) > 0.01) {
        throw new \Exception('Balance calculation error: sum is not zero');
    }

    // Step 5: Optimize transactions
    $creditors = []; // positive balance
    $debtors = [];   // negative balance

    foreach ($balances as $userId => $balance) {
        if ($balance > 0.01) {
            $creditors[] = ['user_id' => $userId, 'amount' => $balance];
        } elseif ($balance < -0.01) {
            $debtors[] = ['user_id' => $userId, 'amount' => abs($balance)];
        }
    }

    // Sort descending by amount
    usort($creditors, fn($a, $b) => $b['amount'] <=> $a['amount']);
    usort($debtors, fn($a, $b) => $b['amount'] <=> $a['amount']);

    $transactions = [];
    $i = 0; // creditor index
    $j = 0; // debtor index

    while ($i < count($creditors) && $j < count($debtors)) {
        $transfer = min($creditors[$i]['amount'], $debtors[$j]['amount']);

        $transactions[] = [
            'from' => $debtors[$j]['user_id'],
            'to' => $creditors[$i]['user_id'],
            'amount' => round($transfer, 2),
        ];

        $creditors[$i]['amount'] -= $transfer;
        $debtors[$j]['amount'] -= $transfer;

        if ($creditors[$i]['amount'] < 0.01) $i++;
        if ($debtors[$j]['amount'] < 0.01) $j++;
    }

    return $transactions;
}
```
