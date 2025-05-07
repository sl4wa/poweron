package main

import (
	"bufio"
	"errors"
	"fmt"
	"io/fs"
	"log"
	"os"
	"path/filepath"
	"strconv"
	"strings"
)

const dataDir = "users/data"

type User struct {
	ChatID     int64
	StreetID   int
	StreetName string
	Building   string

	StartDate string
	EndDate   string
	Comment   string

	sent bool
}

type Outage struct {
	Start        string
	End          string
	City         string
	StreetID     int
	Street       string
	BuildingList string
	Comment      string
}

func buildingMatches(list, building string) bool {
	for _, b := range strings.Split(list, ",") {
		if strings.TrimSpace(b) == building {
			return true
		}
	}
	return false
}

func alreadyNotified(u *User, o *Outage) bool {
	return u.Comment != "" &&
		u.StartDate == o.Start &&
		u.EndDate == o.End &&
		u.Comment == o.Comment
}

func parseUserFile(path string, id int64) (*User, error) {
	f, err := os.Open(path)
	if err != nil {
		return nil, err
	}
	defer f.Close()

	u := &User{ChatID: id}
	sc := bufio.NewScanner(f)
	for sc.Scan() {
		line := sc.Text()
		if parts := strings.SplitN(line, ":", 2); len(parts) == 2 {
			key := strings.TrimSpace(parts[0])
			val := strings.TrimSpace(parts[1])
			switch key {
			case "street_id":
				u.StreetID, _ = strconv.Atoi(val)
			case "street_name":
				u.StreetName = val
			case "building":
				u.Building = val
			case "start_date":
				u.StartDate = val
			case "end_date":
				u.EndDate = val
			case "comment":
				u.Comment = val
			}
		}
	}
	return u, sc.Err()
}

func loadUsers() ([]*User, error) {
	dirEntries, err := os.ReadDir(dataDir)
	if err != nil {
		return nil, err
	}

	var users []*User
	for _, de := range dirEntries {
		if de.IsDir() || !strings.HasSuffix(de.Name(), ".txt") {
			continue
		}
		id, err := strconv.ParseInt(strings.TrimSuffix(de.Name(), ".txt"), 10, 64)
		if err != nil {
			continue
		}
		u, err := parseUserFile(filepath.Join(dataDir, de.Name()), id)
		if err != nil {
			log.Printf("warning: %v", err)
			continue
		}
		users = append(users, u)
	}
	return users, nil
}

func parseOutage(line string) (Outage, bool) {
	const expected = 7
	fields := strings.SplitN(line, "\t", expected)
	if len(fields) != expected {
		return Outage{}, false
	}
	for i := range fields {
		fields[i] = strings.TrimSpace(fields[i])
	}
	streetID, err := strconv.Atoi(fields[3])
	if err != nil {
		return Outage{}, false
	}
	return Outage{
		Start:        fields[0],
		End:          fields[1],
		City:         fields[2],
		StreetID:     streetID,
		Street:       fields[4],
		BuildingList: fields[5],
		Comment:      fields[6],
	}, true
}

func main() {
	users, err := loadUsers()
	if err != nil {
		if errors.Is(err, fs.ErrNotExist) {
			log.Fatalf("data directory %q not found", dataDir)
		}
		log.Fatal(err)
	}

	scanner := bufio.NewScanner(os.Stdin)

	for scanner.Scan() {
		line := scanner.Text()
		if strings.TrimSpace(line) == "" {
			continue
		}

		outage, ok := parseOutage(line)
		if !ok {
			continue
		}

		for _, u := range users {
			if u.sent {
				continue
			}
			if u.StreetID == outage.StreetID && buildingMatches(outage.BuildingList, u.Building) {
				if alreadyNotified(u, &outage) {
					u.sent = true
					continue
				}

				fmt.Printf("%d\t%s\t%s\t%s\t%s\t%s\t%s\n",
					u.ChatID, outage.Start, outage.End, outage.City,
					outage.Street, u.Building, outage.Comment)

				u.sent = true
			}
		}
	}
	if err := scanner.Err(); err != nil {
		log.Fatal(err)
	}
}
