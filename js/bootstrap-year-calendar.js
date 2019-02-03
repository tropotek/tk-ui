/**
 *
 * http://www.bootstrap-year-calendar.com/
 *
 */

jQuery(function ($) {

//  if ($.fn.calendar !== undefined) return;

  var currentYear = 0;
  $('.year-calendar').calendar({
    minDate: new Date(),
    style: 'custom',
    customDataSourceRenderer: function (elt, currentDate, events) {
      elt.parent().css({
        backgroundColor: events[0].color,
        color: '#FFF'
      });
    },
    renderEnd: function (e) {
      var cal = $(this).data('calendar');

      if (cal.getYear() !== currentYear) {
        currentYear = cal.getYear();

        console.log({
          companyId: $(this).data('companyId'),
          subjectId: $(this).data('subjectId'),
          year: currentYear
        });

        $.getJSON($(this).data('src'), {
          companyId: $(this).attr('data-company-id'),
          subjectId: $(this).data('data-subject-id'),
          year: currentYear
        }, function (data) {
          var colorMax = '#5E92C0';
          var colorPlace = '#8cceff';
          if (data.list.length) {
            $.each(data.list, function (i, item) {
              item.startDate = new Date(item.startDate);
              item.endDate = new Date(item.endDate);
              item.color = colorPlace;
              if (item.total >= item.places) {
                item.color = colorMax;
              }
            });
            cal.setDataSource(data.list);
          } else if (cal.tkFlag === undefined && data.first) {
              cal.tkFlag = 'defined';
              var year = parseInt(data.first.dateStart.date.substr(0, 4));
              cal.setYear(year);
          }
        }).fail(function(r, e, m) {
          //console.log(r);
          console.error('Calendar Error: ' + r.responseJSON.message);
        });
      }
    },
    mouseOnDay: function (e) {
      if (e.events.length > 0) {
        var content = '';
        for (var i in e.events) {
          content += '<div class="event-tooltip-content">'
            + '<div class="event-name" style="color:' + e.events[i].color + '">' + e.events[i].name + '</div>'
            + '<div class="event-location">' + e.events[i].location + '</div>'
            + '</div>';
        }
        $(e.element).popover({
          trigger: 'manual',
          container: 'body',
          html: true,
          content: content
        });
        $(e.element).popover('show');
      }
    },
    mouseOutDay: function (e) {
      if (e.events.length > 0) {
        $(e.element).popover('hide');
      }
    },
    selectRange: function (e) {
      // TODO: Could trigger a create placement for the selected dates dialog?
      //console.log({startDate: e.startDate, endDate: e.endDate});
    }

  });

});






