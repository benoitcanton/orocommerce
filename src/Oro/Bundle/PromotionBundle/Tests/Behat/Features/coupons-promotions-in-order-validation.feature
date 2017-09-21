@fixture-OroPromotionBundle:coupons-promotions-in-order-validation.yml
Feature: Coupons Promotions in Order page validation
  In order to find out why coupon not valid during applying it to the order
  As administrator
  I need to have ability to see validation errors for all not valid cases

  Scenario: Apply already applied coupon
    Given I login as administrator
    And go to Sales / Orders
    And click edit FirstOrder in grid
    And click "Promotions and Discounts"
    # apply coupon
    And I click "Add Coupon Code"
    And type "test-1" in "Coupon Code"
    And should see a "Highlighted Suggestion" element
    And click on "Highlighted Suggestion"
    And click "Add" in modal window
    Then I should see next rows in "Added Coupons" table
      | Coupon Code   | Promotion                | Discount Value |
      | test-1        | order Discount Promotion | 50%            |
    And click "Apply" in modal window
    # apply the same coupon again
    When I click "Add Coupon Code"
    And type "test-1" in "Coupon Code"
    And should see a "Highlighted Suggestion" element
    And click on "Highlighted Suggestion"
    And click "Add" in modal window
    Then I should see "This coupon has been already added"
    # save order with applied coupon
    And I click "Cancel" in modal window
    And save form
    And should see "Review Shipping Cost"
    And click "Save" in modal window

  Scenario: Apply coupon for already applied promotion
    Given I click "Promotions and Discounts"
    And click "Add Coupon Code"
    And type "test-2" in "Coupon Code"
    And should see a "Highlighted Suggestion" element
    And click on "Highlighted Suggestion"
    And click "Add" in modal window
    Then I should see "This coupon's discount has been already applied"
    And click "Cancel" in modal window
    And click "Cancel"

  Scenario: Try to add already expired coupon
    Given I go to Marketing / Promotions / Coupons
    # test-3 coupon present but have expired date
    And I should see following grid:
      | Coupon Code | Valid Until           |
      | test-1      | Jan 1, 5000, 12:00 AM |
      | test-2      | Jan 1, 5000, 12:00 AM |
      | test-3      | Jan 1, 1000, 12:00 AM |
    Then I go to Sales / Orders
    And click edit FirstOrder in grid
    And click "Promotions and Discounts"
    And click "Add Coupon Code"
    And click "Coupons Selection Button"
    # expired by date test-3, filtered out and not present for selection
    And I should see following "Coupons Selection" grid:
      | Coupon Code | Valid Until           |
      | test-1      | Jan 1, 5000, 12:00 AM |
      | test-2      | Jan 1, 5000, 12:00 AM |
    And I click "Close Coupons Selection"
    And click "Cancel" in modal window
    And click "Cancel"

  Scenario: Coupon usage limit is exceeded for current customer user
    Given I go to Sales / Orders
    # use test-1 coupon second time
    And click edit SecondOrder in grid
    And click "Promotions and Discounts"
    And click "Add Coupon Code"
    And type "test-1" in "Coupon Code"
    And should see a "Highlighted Suggestion" element
    And click on "Highlighted Suggestion"
    And click "Add" in modal window
    And click "Apply" in modal window
    And save and close form
    And should see "Review Shipping Cost"
    And click "Save" in modal window
    # try to use test-1 third time by one user, limit for it - two times per user
    When I go to Sales / Orders
    And click edit ThirdOrder in grid
    And click "Promotions and Discounts"
    And click "Add Coupon Code"
    And type "test-1" in "Coupon Code"
    And should see a "Highlighted Suggestion" element
    And click on "Highlighted Suggestion"
    And click "Add" in modal window
    And click "Apply" in modal window
    Then I should see "Customer user coupon usage limit is exceeded"
    And click "Cancel" in modal window
    And click "Cancel"

  Scenario: Coupon usage limit is exceeded
    Given I go to Sales / Orders
    # use test-4 coupon first time
    And click edit FirstOrder in grid
    And click "Promotions and Discounts"
    And click "Add Coupon Code"
    And type "test-4" in "Coupon Code"
    And should see a "Highlighted Suggestion" element
    And click on "Highlighted Suggestion"
    And click "Add" in modal window
    And click "Apply" in modal window
    And save and close form
    And should see "Review Shipping Cost"
    And click "Save" in modal window
    # try to use test-4 second time , limit for it - one usage per coupon
    When I go to Sales / Orders
    And click edit SecondOrder in grid
    And click "Promotions and Discounts"
    And click "Add Coupon Code"
    And type "test-4" in "Coupon Code"
    And should see a "Highlighted Suggestion" element
    And click on "Highlighted Suggestion"
    And click "Add" in modal window
    And click "Apply" in modal window
    Then I should see "Coupon usage limit is exceeded"
    And click "Cancel" in modal window
    And click "Cancel"

  Scenario: Try to apply coupon that promotion is not applicable for order
    Given I go to Sales / Orders
    And click edit FirstOrder in grid
    And click "Promotions and Discounts"
    And click "Add Coupon Code"
    And type "test-5" in "Coupon Code"
    And should see a "Highlighted Suggestion" element
    And click on "Highlighted Suggestion"
    And click "Add" in modal window
    And click "Apply" in modal window
    Then I should see "This coupon's discount is not applicable"
    And click "Cancel" in modal window
    And click "Cancel"

  Scenario: Try to apply coupon that have no promotion
    Given I go to Marketing / Promotions / Coupons
    # test-6 coupon present but have no promotion
    And I should see following grid:
      | Coupon Code | Promotion                  |
      | test-1      | order Discount Promotion   |
      | test-2      | order Discount Promotion   |
      | test-3      | order Discount Promotion 2 |
      | test-4      | order Discount Promotion 2 |
      | test-5      | order Discount Promotion 3 |
      | test-6      | N/A                        |
    Then I go to Sales / Orders
    And click edit FirstOrder in grid
    And click "Promotions and Discounts"
    And click "Add Coupon Code"
    And click "Coupons Selection Button"
    # test-6, filtered out and not present for selection
    And I should see following "Coupons Selection" grid:
      | Coupon Code |
      | test-1      |
      | test-2      |
      | test-4      |
      | test-5      |
